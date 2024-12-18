<?php

namespace App\Jobs;

use App\Models\SurveyDelivery;
use App\Models\User;
use App\Models\Survey;
use App\Notifications\UserSurveyNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class IndividualSurveyDeliveryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected SurveyDelivery $surveyDelivery;
    protected User $user;
    protected int $sendingCount = 1;
    /**
     * ジョブのインスタンスを作成
     *
     * @param SurveyDelivery $surveyDelivery
     * @param User $user
     */
    public function __construct(SurveyDelivery $surveyDelivery, User $user)
    {
        $this->surveyDelivery = $surveyDelivery->withoutRelations();
        $this->user = $user;
    }

    /**
     * ジョブの失敗を処理
     *
     * @param Throwable $exception
     */
    public function failed(Throwable $exception): void
    {
        report($exception);
        logger()->error(sprintf('[個人配信失敗] survey_deliveries.id:%s, user.id:%s, error:%s', $this->surveyDelivery->id, $this->user->id, $exception->getMessage()));
    }

    /**
     * ジョブの実行
     */
    public function handle(): void
    {
        logger()->info(sprintf('[個人配信-開始] survey_deliveries.id:%s, user.id:%s', $this->surveyDelivery->id, $this->user->id));

        $this->surveyDelivery->fill(['started_sending_at' => now()])->save();
        $this->surveyDelivery->loadMissing('survey', 'targetable');

        $survey = $this->surveyDelivery->targetable;
        if (!$survey instanceof Survey) {
            logger()->warning(sprintf('[個人配信-無効なターゲット] survey_deliveries.id:%s', $this->surveyDelivery->id));
            return;
        }

        // パスワード生成と保存
        $password = User::generatePassword($survey->id, $this->user->id);
        $this->user->fill(['password' => Hash::make($password)])->save();

        // SurveyAnswer の取得
        $surveyAnswer = $survey
            ->surveyAnswers()
            ->where('survey_target_user_id', $survey->surveyTargetUsers()->where('user_id', $this->user->id)->value('id'))
            ->first();

        if (!$surveyAnswer) {
            logger()->warning(sprintf('[個人配信-回答データなし] user.id:%s', $this->user->id));
            return;
        }

        // メールタイトルと本文の生成
        $emailSubject = SurveyDelivery::swapEmailSubjectTemplate(
            $this->surveyDelivery->subject,
            $survey->company->name,
            $survey->title
        );

        $emailBody = SurveyDelivery::swapEmailBodyTemplate(
            $this->surveyDelivery->body,
            [
                'companyName' => $survey->company->name,
                'formUrl' => $survey->form_url,
                'formPassword' => $survey->form_password,
                'surveyId' => Crypt::encryptString($surveyAnswer->survey_id),
                'customKey' => $surveyAnswer->custom_key,
                'userEmail' => $this->user->email,
                'password' => $password,
                'expiresAt' => $survey->expires_at->format('Y/m/d'),
            ]
        );

        // メール送信
        $this->user->notify(new UserSurveyNotification($emailSubject, $emailBody));

        $this->surveyDelivery
        ->fill([
            'sending_count' => $this->sendingCount,
            'completed_sending_at' => now(),
        ])
        ->save();

        logger()->info(sprintf('[個人配信-送信完了] survey_deliveries.id:%s, user.id:%s', $this->surveyDelivery->id, $this->user->id));
    }
}
