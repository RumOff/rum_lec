<?php

namespace App\Jobs;

use App\Models\SurveyDelivery;
use App\Models\SurveyQuestion;
use App\Models\Survey;
use App\Models\User;
use App\Notifications\UserSurveyNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Throwable;

class SurveyDeliveryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800; // 30分 タイムアウト秒数
    public $tries = 1; // 試行回数

    protected SurveyDelivery $surveyDelivery;
    protected int $sendingCount = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SurveyDelivery $surveyDelivery)
    {
        $this->surveyDelivery = $surveyDelivery->withoutRelations();
    }

    /**
     * ジョブの失敗を処理
     */
    public function failed(Throwable $e): void
    {
        report($e);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 経過観察ログ
        logger()->info(sprintf('[配信-開始]survey_deliveries.id:%s', $this->surveyDelivery->id));

        // すでに配信済みであれば終了
        if (!is_null($this->surveyDelivery->started_sending_at)) {
            // 経過観察ログ
            logger()->info(sprintf('[配信-停止]survey_deliveries.id:%s', $this->surveyDelivery->id));

            return 0;
        }

        // 配信開始
        $this->surveyDelivery->fill(['started_sending_at' => now()])->save();


        // 診断・配信対象データの読み込み
        $this->surveyDelivery->loadMissing('survey', 'targetable');

        // ターゲットタイプで分岐する予定だったが仕様変更で1つに
        if ($this->surveyDelivery->targetable_type !== 'survey') {
            return 0;
        }

        // 配信開始
        $this->multipleDelivery($this->surveyDelivery->targetable);

        // 配信数の保存
        $this->surveyDelivery
            ->fill([
                'sending_count' => $this->sendingCount,
                'completed_sending_at' => now(),
            ])
            ->save();

        // 経過観察ログ
        logger()->info(sprintf('[配信-完了]survey_deliveries.id:%s', $this->surveyDelivery->id));

        return 0;
    }

    /**
     * Survey $targetable
     */
    private function multipleDelivery(Survey $survey)
    {
        // 配信済みユーザーチェック用配列
        $userIds = [];

        $survey->loadMissing('company');
        // 配信開始
        $survey
            ->surveyTargetUsers()
            ->with('user')
            ->whereHas(
                'surveyAnswer',
                fn ($builder) => $builder->whereNull('completes_at')
            )
            ->chunkById(200, function ($surveyTargetUsers) use ($survey, &$userIds) {
                $surveyTargetUsers->each(function ($surveyTargetUser) use ($survey, &$userIds) {
                    try {
                        $user = $surveyTargetUser->user;

                        // ジョブ内でメール配信済みのユーザーはスルーする
                        if (in_array($user->id, $userIds)) {
                            return;
                        } else {
                            $userIds[] = $user->id;
                        }

                        // password作成
                        $password = User::generatePassword($survey->id, $user->id);
                        $user->fill(['password' => Hash::make($password)])->save();

                        // メールタイトルと本文の取得
                        $emailSubject = SurveyDelivery::swapEmailSubjectTemplate(
                            $this->surveyDelivery->subject,
                            $survey->company->name,
                            $survey->title
                        );
                        $emailBody = SurveyDelivery::swapEmailBodyTemplate(
                            $this->surveyDelivery->body,
                            [
                                'companyName' => $survey->company->name,
                                'userName' => $user->name,
                                'userEmail' => $user->email,
                                'password' => $password,
                                'expiresAt' => $survey->expires_at->format('Y/m/d')
                            ]
                        );

                        // ユーザーにメール受診依頼メール送信
                        $user->notify(new UserSurveyNotification($emailSubject, $emailBody));

                        // 経過観察ログ
                        logger()->info(
                            sprintf('[配信-経過]survey_deliveries.id:%s,users.id:%s', $this->surveyDelivery->id, $user->id)
                        );

                        // 配信数++
                        $this->sendingCount++;
                        usleep(500000);
                    } catch (Throwable $e) {
                        report($e);
                    }
                });
            });
    }
}
