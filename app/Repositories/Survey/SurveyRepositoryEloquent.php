<?php

namespace App\Repositories\Survey;

use App\Jobs\SurveyDeliveryJob;
use App\Jobs\CustomizedSurveyDeliveryJob;
use App\Jobs\IndividualSurveyDeliveryJob;
use App\Models\Company;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyAnswerDetail;
use App\Models\SurveyDelivery;
use App\Models\SurveyQuestion;
use App\Models\SurveyTargetUser;
use App\Models\User;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class SurveyRepositoryEloquent implements SurveyRepository
{
    public function __construct(
        Survey $survey,
        SurveyAnswer $surveyAnswer,
        SurveyQuestion $surveyQuestion,
        SurveyTargetUser $surveyTargetUser,
        SurveyAnswerDetail $surveyAnswerDetail,
        SurveyDelivery $surveyDelivery,
        User $user,
        DatabaseManager $db
    ) {
        $this->survey = $survey;
        $this->surveyAnswer = $surveyAnswer;
        $this->surveyQuestion = $surveyQuestion;
        $this->surveyTargetUser = $surveyTargetUser;
        $this->surveyAnswerDetail = $surveyAnswerDetail;
        $this->surveyDelivery = $surveyDelivery;
        $this->user = $user;
        $this->db = $db;
    }

    public function find(int $surveyId): Survey
    {
        return $this->survey->findOrFail($surveyId);
    }

    public function findWithProgress(int $surveyId): Survey
    {
        return $this
            ->survey
            ->withCount([
                // サーベイの総数
                'surveyAnswers as survey_total_count',
                // サーベイの未完了総数
                'surveyAnswers as survey_completed_count' =>
                fn($builder) => $builder->whereNotNull('completes_at')
            ])
            ->findOrFail($surveyId);
    }

    public function findWith(int $surveyId): Survey
    {
        return $this->survey->with('company')->findOrFail($surveyId);
    }

    public function findSurveyAnswer(int $userId, int $surveyAnswerId): SurveyAnswer
    {
        return $this
            ->surveyAnswer
            ->with('surveyTargetUser.targetUser')
            ->whereHas('surveyTargetUser', fn($builder) => $builder->where('user_id', $userId))
            ->findOrFail($surveyAnswerId);
    }
    public function findSurveyAnswerByCustomKey(string $customKey): SurveyAnswer
    {
        return $this
            ->surveyAnswer
            ->where('custom_key', $customKey)
            ->firstOrFail();
    }

    public function firstSurveyAnswerBy(int $surveyTargetId): SurveyAnswer
    {
        return $this
            ->surveyAnswer
            // ->with('surveyTargetUser.targetUser')
            ->where('survey_target_user_id', $surveyTargetId)
            ->firstOrFail();
    }

    public function findTarget(int $surveyTargetUserId): SurveyTargetUser
    {
        $surveyTargetUser = $this
            ->surveyTargetUser
            // ->with('targetUser')
            // ->has('targetUser')
            ->findOrFail($surveyTargetUserId);

        return $surveyTargetUser;
    }

    public function findSurveyDelivery(int $surveyDeliveryId): SurveyDelivery
    {
        return $this->surveyDelivery->findOrFail($surveyDeliveryId);
    }

    public function paginate(int $userId): LengthAwarePaginator
    {
        return $this
            ->survey
            ->withCount([
                // サーベイの総数
                'surveyTargetUsers as survey_total_count' =>
                fn($builder) => $builder->where('user_id', $userId),
                // サーベイの未完了総数
                'surveyTargetUsers as survey_completed_count' =>
                fn($builder) => $builder
                    ->whereHas(
                        'surveyAnswer',
                        fn($builder) => $builder->whereNotNull('completes_at')
                    )
                    ->where('user_id', $userId),
            ])
            ->whereHas(
                'surveyTargetUsers',
                fn($builder) => $builder->where('user_id', $userId)
            )
            ->orderBy('id', 'desc')
            ->paginate(20);
    }

    public function paginateTarget(int $userId, int $surveyId): LengthAwarePaginator
    {
        return $this
            ->surveyTargetUser
            ->with([
                'targetUser',
                'surveyAnswer',
            ])
            ->where([
                'survey_id' => $surveyId,
                'user_id' => $userId,
            ])
            ->orderBy('id', 'asc')
            ->paginate(20);
    }

    public function startsSurvey(SurveyAnswer $surveyAnswer): void
    {
        $surveyAnswer->fill(['starts_at' => now()])->save();
    }

    public function collectSurveyAnswerDetails(SurveyAnswer $surveyAnswer): Collection
    {
        return $this
            ->surveyAnswerDetail
            ->with('surveyQuestion')
            ->where('survey_answer_id', $surveyAnswer->id)
            ->get();
    }

    public function draftSave(SurveyAnswer $surveyAnswer, array $answers): void
    {
        $surveyAnswer->load('surveyAnswerDetails.surveyQuestion');

        $this->db->transaction(function () use ($surveyAnswer, $answers) {
            $surveyAnswer->surveyAnswerDetails->each(function ($detail) use ($answers) {
                $answer = Arr::get($answers, $detail->id);

                if ($detail->surveyQuestion->type === 'text') {
                    $detail->fill([
                        'text' => $answer,
                    ]);
                } else {
                    $detail->fill([
                        'score' => $answer ? Arr::get(SurveyAnswerDetail::SCORES, $answer) : null,
                    ]);
                }
            });
            $surveyAnswer->surveyAnswerDetails()->saveMany($surveyAnswer->surveyAnswerDetails);
            $surveyAnswer->touch();
        });
    }

    public function completesSurvey(SurveyAnswer $surveyAnswer, array $data): array
    {
        try {
            return $this->db->transaction(function () use ($surveyAnswer, $data) {
                // すでに回答が存在するかチェック
                $existingAnswers = SurveyAnswerDetail::where('survey_answer_id', $surveyAnswer->id)->exists();
                if ($existingAnswers) {
                    return [
                        'status' => 'success',
                        'message' => 'Survey answers already exist'
                    ];
                }
                $surveyQuestions = $this->surveyQuestion
                    ->where('survey_id', $surveyAnswer->survey_id)
                    ->get()
                    ->keyBy('sort');

                $answerDetails = [];
                foreach ($data['responses'] as $index => $response) {
                    $sort = $index + 1;
                    $question = $surveyQuestions->get($sort);

                    if (!$question) {
                        throw new \Exception("Question not found for sort: {$sort}");
                    }
                    // 「：」以降の文字列を削除
                    $cleanedAnswer = strstr($response['answer'], ':')
                        ? explode(':', $response['answer'])[1]
                        : $response['answer'];

                    $answerDetails[] = [
                        'survey_answer_id' => $surveyAnswer->id,
                        'survey_question_id' => $question->id,
                        'sort' => $sort,
                        'score' => is_numeric($cleanedAnswer) ? floatval($cleanedAnswer) : null,
                        'text' => !is_numeric($cleanedAnswer) ? $cleanedAnswer : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // バルクインサートの実行
                SurveyAnswerDetail::insert($answerDetails);

                // completes_atカラムを現在の日時に更新
                $surveyAnswer->update(['completes_at' => now()]);

                return ['status' => 'success'];
            });
        } catch (\Exception $e) {
            logger()->error('Survey completion failed: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Failed to complete survey: ' . $e->getMessage()];
        }
    }

    /**
     * 回答文字列をスコアに変換するメソッド
     * @param string $answer
     * @return float|null
     */
    private function convertAnswerToScore(string $answer): ?float
    {
        // 例: 回答に応じたスコアをマッピング
        $mapping = [
            '設問:全く思わない' => 1,
            '設問:思わない' => 2,
            '設問:どちらともいえない' => 3,
            '設問:そう思う' => 4,
            '設問:とてもそう思う' => 5,
        ];

        // マッピングに基づいてスコアを返す
        return $mapping[$answer] ?? null;  // 見つからない場合はnull
    }

    public function completesSurveyByUser(SurveyAnswer $surveyAnswer, array $answers): void
    {
        $surveyAnswer->load('surveyAnswerDetails.surveyQuestion');

        $this->db->transaction(function () use ($surveyAnswer, $answers) {
            $surveyAnswer->surveyAnswerDetails->each(function ($detail) use ($answers) {
                $answer = Arr::get($answers, $detail->id);

                if ($detail->surveyQuestion->type === 'text') {
                    $detail->fill([
                        'text' => $answer,
                    ]);
                } else {
                    $detail->fill([
                        'score' => $answer ? Arr::get(SurveyAnswerDetail::SCORES, $answer) : null,
                    ]);
                }
            });
            $surveyAnswer->surveyAnswerDetails()->saveMany($surveyAnswer->surveyAnswerDetails);
            $surveyAnswer->fill([
                'completes_at' => now(),
            ])->save();
        });
    }

    public function store(
        Company $company,
        array $surveyParam,
        array $userCsv,
    ): void {
        $this->db->transaction(
            function () use ($company, $surveyParam, $userCsv) {
                // 診断登録
                $survey = $company->surveys()->create($surveyParam);

                // ユーザー登録とsurvey_target_user作成
                $this->createUsersAndTargets($company, $survey, $userCsv, $surveyParam['survey_type']);

                // 回答データの作成
                $surveyAnswerData = collect();
                $survey->loadMissing('surveyTargetUsers');
                foreach ($survey->surveyTargetUsers as $surveyTargetUser) {
                    // セキュアでランダムなカスタムキーを生成
                    $customKey = substr(hash('md5', $survey->id . $surveyTargetUser->id . Str::random(4)), 0, 8);
                    $surveyAnswerData->push([
                        'survey_id' => $survey->id,
                        'survey_target_user_id' => $surveyTargetUser->id,
                        'custom_key' => $customKey,
                    ]);
                }
                $survey->surveyAnswers()->createMany($surveyAnswerData->toArray());
            }
        );
    }
    private function createUsersAndTargets($company, $survey, $userCsv, $surveyType)
    {
        $isOpenSurvey = $surveyType === 'open';
        $targetData = collect();

        foreach ($userCsv as $row) {
            // 既存のユーザーを会社IDとメールアドレスで検索

            $existingUser = User::where('company_id', $company->id)
                ->where('email', $row['メールアドレス'])
                ->first();
            // 既存のユーザーがいる場合、その氏名を保持
            $name = $existingUser && !empty($existingUser->name)
                ? $existingUser->name
                : ($isOpenSurvey ? null : $row['氏名']);
            // Userテーブルのデータ
            $userData = [
                'company_id' => $company->id,
                'name' => $name,
                'email' => $row['メールアドレス'],
            ];
            // User作成または更新
            $user = User::updateOrCreate(
                ['company_id' => $company->id, 'email' => $row['メールアドレス']],
                $userData
            );

            // survey_target_userテーブルのデータ
            $targetData->push([
                'survey_id' => $survey->id,
                'user_id' => $user->id,
                'team' => $isOpenSurvey ? null : ($row['チーム名・部署名（自由入力）'] ?? null),
                'post' => $isOpenSurvey ? null : ($row['役職名（自由入力）'] ?? null),
            ]);
        }

        // survey_target_user一括作成
        $survey->surveyTargetUsers()->createMany($targetData);
    }
    public function countSurveyQuestion(int $surveyId): int
    {
        return $this->surveyQuestion->where('survey_id', $surveyId)->count();
    }

    public function collectSurveyDeliveries(int $surveyId): Collection
    {
        return $this
            ->surveyDelivery
            ->with('targetable')
            ->where('targetable_id', $surveyId)
            ->where('targetable_type', Survey::class)
            ->orderBy('scheduled_sending_at', 'desc')
            ->get();
    }
    public function paginateSurveyTargetUsers(int $surveyId, ?string $q): LengthAwarePaginator
    {
        // ベースクエリの作成
        $query = $this
            ->surveyTargetUser
            ->with(['user', 'surveyAnswer']) // 必要な関連データを一括でロード
            ->where('survey_id', $surveyId);

        // 名前検索の条件追加
        if ($q) {
            $query->whereHas('user', fn($builder) => $builder->where('name', 'like', "%${q}%"));
        }

        // ページネーションで100件ずつ取得
        $targets = $query->orderBy('id', 'asc')->paginate(100);

        // チーム単位でデータを一括取得
        $teamGroups = $this
            ->surveyTargetUser
            ->with(['user', 'surveyAnswer'])
            ->where('survey_id', $surveyId)
            ->get()
            ->groupBy('team'); // チームごとにグループ化

        // 各ターゲットに所属チームのデータを追加
        $targets->each(function ($target) use ($teamGroups) {
            $target['raters'] = $teamGroups->get($target->team) ?? collect([]);
        });

        return $targets;
    }

    // public function paginateSurveyTargetUsers(int $surveyId, ?string $q): LengthAwarePaginator
    // {
    //     $targets = $this
    //         ->surveyTargetUser
    //         ->with('user', 'surveyAnswer')
    //         ->where([
    //             'survey_id' => $surveyId,
    //         ])
    //         ->when(
    //             $q,
    //             fn($query) => $query->whereHas(
    //                 'user',
    //                 fn($builder) => $builder->where('name', 'like', "%${q}%")
    //             )
    //         )
    //         // ->orderBy('team', 'asc')
    //         ->orderBy('id', 'asc')
    //         ->paginate(100);

    //     $targets->each(function ($target) {
    //         $target['raters'] = $this
    //             ->surveyTargetUser
    //             ->with('user', 'surveyAnswer')
    //             ->where([
    //                 'survey_id' => $target->survey_id,
    //                 'team' => $target->team
    //             ])
    //             ->get();
    //     });

    //     return $targets;
    // }

    public function storeSurveyDelivery(Survey $survey, array $params): void
    {
        $this->db->transaction(function () use ($survey, $params) {
            $surveyDelivery = $survey
                ->surveyDeliveries()
                ->create(
                    $params + [
                        'targetable_id' => $survey->id,
                        'targetable_type' => Survey::class,
                    ]
                );

            // Job登録
            $job = (new CustomizedSurveyDeliveryJob($surveyDelivery))->delay($surveyDelivery->scheduled_sending_at);
            $jobId = app(Dispatcher::class)->dispatch($job);
            $surveyDelivery->fill(['job_id' => $jobId])->save();
        });
    }

    public function update(Survey $survey, array $surveyParam): void
    {
        $survey->fill($surveyParam)->save();
    }

    public function storeTarget(array $params, int $surveyId): void
    {
        $survey = $this->survey->find($surveyId);
        if (!$survey) {
            throw new \Exception('Survey not found.');
        }
        // ユーザーが存在するか確認 (company_id と email の一致で判断)
        $user = $this->user
            ->where('company_id', $survey->company_id)
            ->where('email', $params['email'])
            ->first();
        // ユーザーが存在しない場合は新規作成
        if (!$user) {
            $user = $this->user->create([
                'company_id' => $survey->company_id,
                'name' => $params['name'] ?? null,
                'email' => $params['email'],
            ]);
        }
        // target_user テーブルに追加
        $surveyTargetUser = $this->surveyTargetUser->create([
            'survey_id' => $survey->id,
            'user_id' => $user->id,
            'team' => $params['team'] ?? null,
            'post' => $params['post'] ?? null,
        ]);
        $customKey = substr(hash('md5', $survey->id . $surveyTargetUser->id . Str::random(4)), 0, 8);
        // survey_answers テーブルに追加
        $this->surveyAnswer->create([
            'survey_id' => $survey->id,
            'survey_target_user_id' => $surveyTargetUser->id,
            'custom_key' => $customKey,
        ]);
        // 最新のメール配信情報を取得
        $latestDelivery = $this->surveyDelivery
            ->where('targetable_id', $survey->id)
            ->orderByDesc('completed_sending_at')
            ->first();
        if ($latestDelivery) {
            // 新しいメールアドレスに個別配信
            $this->storeSurveyIndividualDelivery($user, $latestDelivery);
        }
    }

    public function updateTarget(array $params, int $surveyTargetUserId): void
    {
        // 該当するターゲットユーザーと関連ユーザーを取得
        $surveyTargetUser = $this->surveyTargetUser->find($surveyTargetUserId);
        $existingUser = $surveyTargetUser->user;
        // Email 更新確認
        $isEmailUpdated = $existingUser && $existingUser->email !== $params['email'];
        $surveyTargetUser->update([
            'team' => $params['team'],
            'post' => $params['post'],
        ]);
        $existingUser->update([
            'name' => $params['name'],
            'email' => $params['email'],
        ]);
        // 更新後の情報を取得
        $existingUser->refresh();
        // Email 更新時の処理
        if ($isEmailUpdated) {
            // 該当する survey_answer を取得
            $surveyAnswer = $this->surveyAnswer
                ->where('survey_target_user_id', $surveyTargetUser->id)
                ->first();
            // completes_at が設定されている場合、メール送信をスキップ
            if ($surveyAnswer && $surveyAnswer->completes_at !== null) {
                logger()->info(sprintf(
                    'Email not sent because survey is already completed: user.id=%d, survey_target_user.id=%d',
                    $existingUser->id,
                    $surveyTargetUser->id
                ));
                return;
            }
            // 最新のメール配信情報を取得
            $latestDelivery = $this->surveyDelivery
                ->where('targetable_id', $surveyTargetUser->survey_id)
                ->orderByDesc('completed_sending_at')
                ->first();
            if ($latestDelivery) {
                // 新しいメールアドレスに個別配信
                $this->storeSurveyIndividualDelivery($existingUser, $latestDelivery);
            }
        }
    }

    public function storeSurveyIndividualDelivery(User $user, $delivery): void
    {
        $this->db->transaction(function () use ($user, $delivery) {
            $surveyDelivery = $this->surveyDelivery
                ->create([
                    'targetable_id' => $delivery->targetable_id,
                    'targetable_type' => Survey::class,
                    'subject' => $delivery->subject,
                    'body' => $delivery->body,
                    'scheduled_sending_at' => now(), // 即時配信
                ]);
            $job = new IndividualSurveyDeliveryJob($surveyDelivery, $user);
            $jobId = app(Dispatcher::class)->dispatch($job);
            $surveyDelivery->fill(['job_id' => $jobId])->save();
        });
    }

    public function updateDetails(array $surveyParam): void
    {
        foreach ($surveyParam as $param) {
            $this
                ->surveyAnswerDetail
                ->where('id', $param['id'])
                ->update(['text' => $param['text']]);
        }
    }

    public function hasCategory(int $surveyId): bool
    {
        return $this
            ->surveyQuestion
            ->where('survey_id', $surveyId)
            ->where(
                fn($builder) => $builder
                    ->whereNotNull('major_category')
                    ->orWhereNotNull('medium_category')
                    ->orWhereNotNull('minor_category')
            )
            ->exists();
    }

    public function collectQuestion(int $surveyId): Collection
    {
        return $this
            ->surveyQuestion
            ->where('survey_id', $surveyId)
            ->orderBy('sort')
            ->get();
    }

    public function collectRadioQuestionForResults(int $surveyId): array
    {
        $questions = $this
            ->surveyQuestion
            ->where('survey_id', $surveyId)
            ->orderBy('sort')
            ->get();

        $data = [];

        foreach ($questions as $key => $q) {
            if (Arr::exists($questions, $key - 1)) {
                $previous = $questions[($key - 1)];

                if ($previous->category === $q->category) {
                    $category['count']++;

                    if ($previous->major_category === $q->major_category) {
                        $featureCategory['count']++;
                        $items[] = $q;

                        continue;
                    } else {
                        $featureCategory['items'] = $items;
                        $category['major_category'][] = $featureCategory;

                        $items = [$q];
                        $featureCategory = [
                            'count' => 1,
                            'name' => $q->major_category,
                            'items' => []
                        ];

                        continue;
                    }
                } else {
                    $featureCategory['items'] = $items;
                    $category['major_category'][] = $featureCategory;
                    $data = array_merge($data, [$category]);

                    $items = [$q];
                    $featureCategory = [
                        'count' => 1,
                        'name' => $q->major_category,
                        'items' => []
                    ];
                    $category = [
                        'count' => 1,
                        'name' => $q->category,
                        'major_category' => [],
                    ];

                    continue;
                }
            } else {
                // 最初のloopはここ
                $items = [$q];
                $featureCategory = [
                    'count' => 1,
                    'name' => $q->major_category,
                    'items' => []
                ];
                $category = [
                    'count' => 1,
                    'name' => $q->category,
                    'major_category' => []
                ];

                continue;
            }
        }
        $featureCategory['items'] = $items;
        $category['major_category'][] = $featureCategory;
        $data = array_merge($data, [$category]);

        return $data;
    }

    public function collectTextQuestionForResults(int $surveyId): Collection
    {
        // return $this
        //     ->surveyQuestion
        //     ->where([
        //         'survey_id' => $surveyId,
        //         'major_category' => 'text',
        //     ])
        //     ->orderBy('sort')
        //     ->get();
        return $this->surveyQuestion
            ->where('survey_id', $surveyId)
            ->whereJsonContains('answer_options', 'text')
            ->orderBy('sort')
            ->get();
    }

    //設問編集かどうか　CSで編集できないから必要ないと予想
    public function canEditQuestion(int $surveyId): bool
    {
        $survey = $this->survey->findOrFail($surveyId);

        if ($survey->surveyDeliveries()->whereNotNull('started_sending_at')->exists()) {
            return false;
        }
        if ($survey->surveyAnswers()->whereNotNull('starts_at')->exists()) {
            return false;
        }

        return true;
    }

    public function storeQuestions(array $sampleAnswer): void
    {
        $surveyId = Crypt::decryptString($sampleAnswer['survey_id']);

        $this->db->transaction(function () use ($surveyId, $sampleAnswer) {
            $existingQuestions = $this->surveyQuestion->where('survey_id', $surveyId)->exists();
            if (!$existingQuestions) {
                // 設問登録
                $this->createQuestions($surveyId, $sampleAnswer['responses']);
            }
        });
    }

    private function createQuestions(int $surveyId, array $responses): void
    {
        $survey = $this->survey->find($surveyId);
        if (!$survey) {
            throw new \Exception("Survey with id {$surveyId} not found.");
        }
        $questions = [];
        foreach ($responses as $index => $response) {
            $questions[] = [
                'survey_id' => $surveyId,
                'sort' => $index + 1,
                'major_category' => $response['major_category'],
                'medium_category' => $response['medium_category'],
                'minor_category' => $response['minor_category'],
                'question_text' => $response['question_text'],
                'answer_options' => json_encode($response['answer_options']),
            ];
        }
        $this->surveyQuestion->insert($questions);
    }

    public function updateQuestions(Survey $survey, array $params): void
    {
        $this->db->transaction(function () use ($survey, $params) {
            $questions = $survey->surveyQuestions()->orderBy('sort')->get();

            foreach ($params as $questionId => $param) {
                $q = $questions->where('id', $questionId)->first();
                $q->fill($param)->save();
            }
        });
    }

    public function collectFormatedTargetSurveyAnswerDetails(int $surveyId): SupportCollection
    {
        $results = $this
            ->surveyTargetUser
            ->where([
                'survey_target_users.survey_id' => $surveyId,
            ])
            ->join('survey_answers', 'survey_answers.survey_target_user_id', '=', 'survey_target_users.id')
            ->join('survey_answer_details', 'survey_answer_details.survey_answer_id', '=', 'survey_answers.id')
            ->orderBy('survey_target_users.id', 'asc')
            ->select([
                'survey_target_users.id as id',
                'survey_answers.id as survey_answer_id',
                'survey_answer_details.id as survey_answer_detail_id',
                'survey_target_users.survey_id as survey_id',
                'user_id',
                // 'target_user_id',
                'role',
                'team',
                // 'post',
                'survey_target_user_id',
                // 'starts_at',
                'completes_at',
                'survey_answer_id',
                'survey_question_id',
                'score',
                'text',
            ])
            ->get();

        return $results->groupBy('survey_question_id');
    }

    public function collectFormatedOtherSurveyAnswerDetails(int $surveyId): SupportCollection
    {
        $results = $this
            ->surveyTargetUser
            ->where([
                'survey_target_users.survey_id' => $surveyId,
            ])
            ->join('survey_answers', 'survey_answers.survey_target_user_id', '=', 'survey_target_users.id')
            ->join('survey_answer_details', 'survey_answer_details.survey_answer_id', '=', 'survey_answers.id')
            ->orderBy('survey_target_users.id', 'asc')
            ->select([
                'survey_target_users.id as id',
                'survey_answers.id as survey_answer_id',
                'survey_answer_details.id as survey_answer_detail_id',
                'survey_target_users.survey_id as survey_id',
                'user_id',
                // 'target_user_id',
                // 'role',
                'team',
                'post',
                'survey_target_user_id',
                // 'starts_at',
                'completes_at',
                'survey_answer_id',
                'survey_question_id',
                'score',
                'text',
            ])
            ->get();

        return $results->groupBy([
            'post',
            'survey_question_id'
        ]);
    }

    // public function collectFormatedSurveyAnswerDetails(SurveyTargetUser $surveyTargetUser): SupportCollection
    // {
    //     $results = $this
    //         ->surveyTargetUser
    //         ->where([
    //             'survey_target_users.survey_id' => $surveyTargetUser->survey_id,
    //             // 'survey_target_users.target_user_id' => $surveyTargetUser->target_user_id,
    //             'survey_target_users.team' => $surveyTargetUser->team,
    //         ])
    //         ->join('survey_answers', 'survey_answers.survey_target_user_id', '=', 'survey_target_users.id')
    //         ->join('survey_answer_details', 'survey_answer_details.survey_answer_id', '=', 'survey_answers.id')
    //         ->orderBy('survey_target_users.id', 'asc')
    //         ->select([
    //             'survey_target_users.id as id',
    //             'survey_answers.id as survey_answer_id',
    //             'survey_answer_details.id as survey_answer_detail_id',
    //             'survey_target_users.survey_id as survey_id',
    //             'user_id',
    //             // 'target_user_id',
    //             // 'role',
    //             'team',
    //             'post',
    //             'survey_target_user_id',
    //             // 'starts_at',
    //             'completes_at',
    //             'survey_answer_id',
    //             'survey_question_id',
    //             'score',
    //             'text',
    //         ])
    //         ->get();

    //     return $results->groupBy([
    //         // 'role',
    //         'post',
    //         'survey_question_id'
    //     ]);
    // }
    public function collectFormatedSurveyAnswerDetails(SurveyTargetUser $surveyTargetUser): SupportCollection
    {
        $results = $this->surveyAnswerDetail
            ->join('survey_answers', 'survey_answers.id', '=', 'survey_answer_details.survey_answer_id')
            ->join('survey_questions', 'survey_questions.id', '=', 'survey_answer_details.survey_question_id')
            ->where('survey_answers.survey_target_user_id', $surveyTargetUser->id)
            ->select([
                'survey_answer_details.id as survey_answer_detail_id',
                'survey_answers.id as survey_answer_id',
                'survey_questions.id as survey_question_id',
                'survey_questions.sort',
                'survey_questions.major_category',
                'survey_questions.medium_category',
                'survey_questions.minor_category',
                'survey_questions.question_text',
                'survey_answer_details.score',
                'survey_answer_details.text',
                'survey_answers.completes_at'
            ])
            ->orderBy('survey_questions.sort')
            ->get();

        return $results->groupBy('survey_question_id');
    }
    public function deleteSurveyDelivery(SurveyDelivery $surveyDelivery): void
    {
        $this->db->transaction(function () use ($surveyDelivery) {
            // Jobを削除
            DB::table('jobs')->where('id', $surveyDelivery->job_id)->delete();
            // Job登録
            $surveyDelivery->delete();
        });
    }

    public function deleteSurvey(Survey $survey): void
    {
        $survey->delete();
    }

    public function deleteTarget(SurveyTargetUser $surveyTargetUser): void
    {
        $surveyAnswers = $this->surveyAnswer
            ->where('survey_target_user_id', $surveyTargetUser->id)
            ->get();

        $surveyAnswerIds = $surveyAnswers->pluck('id'); // idのコレクションを取得
        $this->surveyAnswerDetail
            ->whereIn('survey_answer_id', $surveyAnswerIds)
            ->delete();

        $this->surveyAnswer
            ->whereIn('id', $surveyAnswerIds)
            ->delete();

        $surveyTargetUser->delete();
    }

    public function collectSurveyTargetUserBy(int $userId): Collection
    {
        $targets = $this
            ->surveyTargetUser
            ->with('user.company', 'surveyAnswer', 'survey')
            ->where([
                // 'target_user_id' => $userId,
            ])
            ->has('survey')
            ->orderBy('id', 'asc')
            ->get();

        $targets->each(function ($target) {
            $target['raters'] = $this
                ->surveyTargetUser
                ->with('user', 'surveyAnswer')
                ->where([
                    'survey_id' => $target->survey_id,
                    'team' => $target->team
                ])
                ->get();
        });

        return $targets;
    }

    public function collectAll(): Collection
    {
        return $this->survey->with('company')->orderBy('id', 'desc')->limit(30)->get();
    }

    public function collectQuestionForDownload(array $params): array
    {
        $surveys = $this
            ->survey
            ->with([
                'surveyQuestions' => fn($builder) => $builder->orderBy('sort', 'asc')
            ])
            ->when(Arr::get($params, 'companyId'), function ($query, $companyId) {
                $query->where('company_id', $companyId)->orderBy('id', 'ASC');
            })
            ->when(Arr::get($params, 'surveyId'), function ($query, $surveyId) {
                $query->where('id', $surveyId)->orderBy('id', 'ASC');
            })
            ->when(Arr::get($params, 'period'), function ($query, $period) {
                $query
                    ->whereHas(
                        'surveyAnswers',
                        fn($builder) => $builder->whereBetween('completes_at', $period)
                    )
                    ->orderBy('id', 'ASC');
            })
            ->get();

        $questions = $surveys->map(function ($survey, $key) {
            return $survey->surveyQuestions->map(function ($surveyQuestion, $key) {
                return "[設問{$surveyQuestion->id}] {$surveyQuestion->question_text}";
            });
        });

        $ids = $surveys->map(function ($survey, $key) {
            return $survey->surveyQuestions->pluck('id');
        });

        return [
            $questions->flatten()->toArray(),
            $ids->flatten()->toArray(),
        ];
    }

    public function builderDownloadDataBy(array $params): Builder
    {
        return $this
            ->surveyAnswer
            ->with([
                'survey.company',
                'surveyAnswerDetails',
                'surveyTargetUser' => fn($builder) => $builder->with('user', 'targetUser')
            ])
            ->whereNotNull('completes_at')
            ->has('survey')
            ->when(Arr::get($params, 'companyId'), function ($query, $companyId) {
                $query
                    ->whereHas(
                        'survey',
                        fn($builder) => $builder->whereHas(
                            'company',
                            fn($builder) => $builder->where('id', $companyId)
                        )
                    )
                    ->orderBy('id', 'ASC');
            })
            ->when(Arr::get($params, 'surveyId'), function ($query, $surveyId) {
                $query->where('survey_id', $surveyId)->orderBy('id', 'ASC');
            })
            ->when(Arr::get($params, 'period'), function ($query, $period) {
                $query->whereBetween('completes_at', $period)->orderBy('completes_at', 'ASC');
            });
    }
}
