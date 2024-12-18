<?php

namespace App\Jobs;

use App\Models\SurveyDelivery;
use App\Models\Survey;
use App\Models\User;
use App\Notifications\UserSurveyNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Throwable;
use Illuminate\Support\Facades\Crypt;

class CustomizedSurveyDeliveryJob implements ShouldQueue
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
        logger()->error(sprintf('[配信失敗]survey_deliveries.id:%s, error:%s', $this->surveyDelivery->id, $e->getMessage()));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        logger()->info(sprintf('[カスタム配信-開始]survey_deliveries.id:%s targetable_type:%s', $this->surveyDelivery->id, $this->surveyDelivery->targetable_type));


        if (!is_null($this->surveyDelivery->started_sending_at)) {
            logger()->info(sprintf('[カスタム配信-開始]survey_deliveries.id:%s targetable_type:%s', $this->surveyDelivery->id, $this->surveyDelivery->targetable_type));
            return;
        }

        $this->surveyDelivery->fill(['started_sending_at' => now()])->save();

        $this->surveyDelivery->loadMissing('survey', 'targetable');

        if ($this->surveyDelivery->targetable_type !== Survey::class) {
            logger()->warning(sprintf('[カスタム配信-無効なターゲット]survey_deliveries.id:%s targetable_type:%s', $this->surveyDelivery->id, $this->surveyDelivery->targetable_type));
            return;
        }

        $this->customizedMultipleDelivery($this->surveyDelivery->targetable);

        $this->surveyDelivery
            ->fill([
                'sending_count' => $this->sendingCount,
                'completed_sending_at' => now(),
            ])
            ->save();

        logger()->info(sprintf('[カスタム配信-完了]survey_deliveries.id:%s', $this->surveyDelivery->id));
    }

    /**
     * カスタマイズされた複数配信
     */
    private function customizedMultipleDelivery(Survey $survey)
    {
        $survey->loadMissing('company');

        $survey
            ->surveyTargetUsers()
            ->with('user')
            ->whereHas(
                'surveyAnswer',
                fn($builder) => $builder->whereNull('completes_at')
            )
            ->chunkById(200, function ($surveyTargetUsers) use ($survey) {
                foreach ($surveyTargetUsers as $surveyTargetUser) {
                    try {
                        $this->sendCustomizedEmail($survey, $surveyTargetUser->user);
                        $this->sendingCount++;
                        usleep(500000); // 0.5秒のスリープ
                    } catch (Throwable $e) {
                        logger()->error(sprintf(
                            '[カスタム配信-エラー]survey_deliveries.id:%s, user.id:%s, error:%s',
                            $this->surveyDelivery->id,
                            $surveyTargetUser->user->id,
                            $e->getMessage()
                        ));
                    }
                }
            });
    }

    /**
     * カスタマイズされたメールの送信
     */
    private function sendCustomizedEmail(Survey $survey, User $user)
    {
        $password = User::generatePassword($survey->id, $user->id);
        $user->fill(['password' => Hash::make($password)])->save();
        // SurveyTargetUserテーブルを通じてSurveyAnswerを取得
        $surveyTargetUser = $survey
            ->surveyTargetUsers()
            ->where('user_id', $user->id) // SurveyTargetUserテーブルのuser_idで検索
            ->first();

        if (!$surveyTargetUser) {
            logger()->warning('SurveyTargetUser not found for user: ' . $user->id);
            return;
        }

        // SurveyTargetUserに紐付いたSurveyAnswerを取得
        $surveyAnswer = $survey
            ->surveyAnswers()
            ->where('survey_target_user_id', $surveyTargetUser->id) // SurveyTargetUserのIDを使用
            ->first();
        // メールタイトルと本文の取得
        $emailSubject = SurveyDelivery::swapEmailSubjectTemplate(
            $this->surveyDelivery->subject,
            $survey->company->name,
            $survey->title
        );

        $emailBody = SurveyDelivery::swapEmailBodyTemplate(
            $this->surveyDelivery->body,
            [
                // 'name' => $user->name,
                'companyName' => $survey->company->name,
                'formUrl' => $survey->form_url,
                'formPassword' => $survey->form_password,
                'surveyId' => Crypt::encryptString($surveyAnswer->survey_id),
                'customKey' => $surveyAnswer->custom_key,
                'userEmail' => $user->email,
                'password' => $password,
                'expiresAt' => $survey->expires_at->format('Y/m/d')
            ]
        );

        $user->notify(new UserSurveyNotification($emailSubject, $emailBody));

        logger()->info(sprintf('[カスタム配信-送信完了]survey_deliveries.id:%s, user.id:%s', $this->surveyDelivery->id, $user->id));
    }

    // /**
    //  * カスタムリンクの生成
    //  */
    // private function generateCustomLink(Survey $survey, User $user): string
    // {
    //     $baseUrl = 'https://example.com'; // 環境変数から取得するのが望ましいです
    //     $token = $this->generateSecureToken($survey, $user);

    //     return "{$baseUrl}/{$user->id}?token={$token}";
    // }

    // /**
    //  * セキュアなトークンの生成
    //  */
    // private function generateSecureToken(Survey $survey, User $user): string
    // {
    //     $data = $user->id . $survey->id . now()->timestamp;
    //     return hash_hmac('sha256', $data, config('app.key'));
    // }

    /**
     * メール本文のカスタマイズ
     */
    // private function customizeEmailBody(Survey $survey, User $user, string $password, string $customLink): string
    // {
    //     $body = SurveyDelivery::swapEmailBodyTemplate(
    //         $this->surveyDelivery->body,
    //         $survey->company->name,
    //         $user->name,
    //         $user->email,
    //         $password,
    //         $survey->expires_at->format('Y/m/d')
    //     );

    //     // カスタムリンクの置換
    //     return str_replace('{カスタムリンク}', $customLink, $body);
    // }
}
