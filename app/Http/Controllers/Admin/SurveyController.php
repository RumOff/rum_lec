<?php

namespace App\Http\Controllers\Admin;

use App\Models\SurveyQuestion;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConfirmSurveyRequest;
use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use App\Repositories\Company\CompanyRepository;
use App\Repositories\Survey\SurveyRepository;
use App\Repositories\User\UserRepository;
use App\Services\Csv\Upload as CsvUploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurveyController extends Controller
{
    public function __construct(CompanyRepository $companyRepo, SurveyRepository $surveyRepo, UserRepository $userRepo)
    {
        $this->companyRepo = $companyRepo;
        $this->surveyRepo = $surveyRepo;
        $this->userRepo = $userRepo;
    }

    public function create(int $companyId)
    {
        $company = $this->companyRepo->find($companyId);
        return view('admin.surveys.create')->with([
            'company' => $company,
        ]);
    }

    public function confirm(
        CsvUploadService $csvUpload,
        ConfirmSurveyRequest $request,
        int $companyId
    ) {
        $company = $this->companyRepo->find($companyId);
        $userPath = $csvUpload->upload($request->file('csv_users'));

        // サーベイ種別に応じてバリデーションを変更
        if ($request->input('survey_type') === 'open') {
            $userValidateMessages = $csvUpload
                ->validate(
                    '対象者CSV',
                    $userPath,
                    User::OPEN_CSV_HEADER,
                    User::OPEN_CSV_HEADER_VALIDATION,
                    $max = 1000
                );
        } else {
            $userValidateMessages = $csvUpload
                ->validate(
                    '対象者CSV',
                    $userPath,
                    User::SPECIFIED_CSV_HEADER,
                    User::SPECIFIED_CSV_HEADER_VALIDATION,
                    $max = 1000
                );
        }
        if (! empty($userValidateMessages)) {
            return back()
                ->withErrors($userValidateMessages)
                ->withInput();
        }
        return view('admin.surveys.confirm')->with([
            'company' => $company,
            'title' => $request->input('title'),
            'startsAt' => $request->input('starts_at'),
            'expiresAt' => $request->input('expires_at'),
            'sendInvitationAt' => $request->input('send_invitation_at'),
            'sendReminderAt' => $request->input('send_reminder_at'),
            'formUrl' => $request->input('form_url'),
            'formPassword' => $request->input('form_password'),
            'emailSubject' => $request->input('email_subject'),
            'emailBody' => $request->input('email_body'),
            'userPath' => $userPath,
            'userCsv' => $csvUpload->getCsvDataWithoutHeader($userPath),
            'surveyType' => $request->input('survey_type')
        ]);
    }

    public function store(
        CsvUploadService $csvUpload,
        StoreSurveyRequest $request,
        int $companyId
    ) {
        $company = $this->companyRepo->find($companyId);
        $title = $request->input('title');
        $form_url = $request->input('form_url');
        $form_password = $request->input('form_password');
        $starts_at = $request->input('starts_at');
        $expires_at = $request->input('expires_at');
        $survey_type = $request->input('survey_type');
        $this->surveyRepo->store(
            $company,
            compact('title', 'form_url', 'form_password', 'starts_at', 'expires_at', 'survey_type'),
            $csvUpload->getCsvDataWithoutHeader($request->input('user_path')),
        );

        // 二重送信防止
        $request->session()->regenerateToken();

        return view('admin.surveys.store')->with([
            'company' => $company,
            'userCount' => $csvUpload->count($request->input('user_path')),
        ]);
    }

    public function show(Request $request, int $companyId, int $surveyId)
    {
        $company = $this->companyRepo->find($companyId);
        $survey = $this->surveyRepo->findWithProgress($surveyId);
        $countSurveyQuestion = $this->surveyRepo->countSurveyQuestion($surveyId);
        $surveyDeliveries = $this->surveyRepo->collectSurveyDeliveries($surveyId);
        $surveyTargetUsers = $this->surveyRepo->paginateSurveyTargetUsers($surveyId, $request->input('q'));

        return view('admin.surveys.show')->with([
            'company' => $company,
            'survey' => $survey,
            'countSurveyQuestion' => $countSurveyQuestion,
            'surveyDeliveries' => $surveyDeliveries,
            'surveyTargetUsers' => $surveyTargetUsers,
        ]);
    }

    public function edit(int $companyId, int $surveyId)
    {
        $company = $this->companyRepo->find($companyId);
        $survey = $this->surveyRepo->find($surveyId);

        return view('admin.surveys.edit')->with([
            'company' => $company,
            'survey' => $survey,
        ]);
    }

    public function update(UpdateSurveyRequest $request, int $companyId, int $surveyId)
    {
        $company = $this->companyRepo->find($companyId);
        $survey = $this->surveyRepo->find($surveyId);

        $this->surveyRepo->update($survey, $request->all());

        // 二重送信防止
        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.companies.surveys.show', [$company, $survey])
            ->with('success', ['変更しました']);
    }

    public function destroy(int $companyId, int $surveyId)
    {
        $company = $this->companyRepo->find($companyId);
        $survey = $this->surveyRepo->find($surveyId);

        $this->surveyRepo->deleteSurvey($survey);

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('success', ['診断を削除しました']);
    }

    public function resultsAll(int $companyId, int $surveyId)
    {
        $company = $this->companyRepo->find($companyId);
        $survey = $this->surveyRepo->find($surveyId);

        $hasCategory = $this->surveyRepo->hasCategory($surveyId);
        $questions = $this->surveyRepo->collectRadioQuestionForResults($surveyId);
        $targetSurveyAnswerDetails = $this->surveyRepo->collectFormatedTargetSurveyAnswerDetails($surveyId);
        $otherSurveyAnswerDetails = $this->surveyRepo->collectFormatedOtherSurveyAnswerDetails($surveyId);

        return view('admin.surveys.results-all')->with([
            'company' => $company,
            'survey' => $survey,
            'hasCategory' => $hasCategory,
            'questions' => $questions,
            'targetSurveyAnswerDetails' => $targetSurveyAnswerDetails,
            'otherSurveyAnswerDetails' => $otherSurveyAnswerDetails,
        ]);
    }

    public function results(int $companyId, int $surveyId, int $surveyTargetUserId)
    {
        $company = $this->companyRepo->find($companyId);
        $survey = $this->surveyRepo->find($surveyId);

        $surveyTargetUser = $this->surveyRepo->findTarget($surveyTargetUserId);
        $surveyAnswer = $this->surveyRepo->firstSurveyAnswerBy($surveyTargetUserId);

        // カテゴリ情報を含めて質問を取得
        $questions = $this->surveyRepo->collectQuestion($surveyId);
        $surveyTargetUser = $this->surveyRepo->findTarget($surveyTargetUserId);

        // 回答詳細を取得
        $surveyAnswerDetails = $this->surveyRepo->collectFormatedSurveyAnswerDetails($surveyTargetUser);

        return view('admin.surveys.results')->with([
            'company' => $company,
            'survey' => $survey,
            'surveyAnswer' => $surveyAnswer,
            'surveyAnswerDetails' => $surveyAnswerDetails,
        ]);
    }

    public function editResults(int $companyId, int $surveyId, int $surveyTargetUserId)
    {
        $company = $this->companyRepo->find($companyId);
        $survey = $this->surveyRepo->find($surveyId);

        $surveyTargetUser = $this->surveyRepo->findTarget($surveyTargetUserId);
        $textQuestions = $this->surveyRepo->collectTextQuestionForResults($surveyId);

        $surveyAnswer = $this->surveyRepo->firstSurveyAnswerBy($surveyTargetUserId);
        $surveyAnswerDetails = $this->surveyRepo->collectFormatedSurveyAnswerDetails($surveyTargetUser);

        return view('admin.surveys.results-edit')->with([
            'company' => $survey->company,
            'survey' => $survey,
            'textQuestions' => $textQuestions,
            'surveyAnswer' => $surveyAnswer,
            'surveyAnswerDetails' => $surveyAnswerDetails,
        ]);
    }

    public function updateResults(Request $request, int $companyId, int $surveyId, int $surveyTargetUserId)
    {
        $company = $this->companyRepo->find($companyId);
        $survey = $this->surveyRepo->find($surveyId);

        $this->surveyRepo->updateDetails($request->except(['_method', '_token']));

        // 二重送信防止
        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.companies.surveys.show', [$company, $survey])
            ->with('success', ['変更しました']);
    }

    public function createUser(int $companyId, int $surveyId)
    {
        $company = $this->companyRepo->find($companyId);
        $survey = $this->surveyRepo->find($surveyId);

        return view('admin.surveys.user-create')->with([
            'company' => $survey->company,
            'survey' => $survey,
        ]);
    }

    public function storeUser(Request $request, int $companyId, int $surveyId)
    {
        $company = $this->companyRepo->find($companyId);
        $survey = $this->surveyRepo->find($surveyId);
        $this->surveyRepo->storeTarget($request->all(),$surveyId);
        // 二重送信防止
        $request->session()->regenerateToken();
        return redirect()
            ->route('admin.companies.surveys.show', [$company, $survey])
            ->with('success', ['作成しました']);
    }

    public function editUser(int $companyId, int $surveyId, int $surveyTargetUserId)
    {
        $company = $this->companyRepo->find($companyId);
        $surveyTargetUser = $this->surveyRepo->findTarget($surveyTargetUserId);
        $survey = $this->surveyRepo->find($surveyId);

        $surveyAnswer = $this->surveyRepo->firstSurveyAnswerBy($surveyTargetUserId);
        $surveyAnswerDetails = $this->surveyRepo->collectFormatedSurveyAnswerDetails($surveyTargetUser);

        return view('admin.surveys.user-edit')->with([
            'company' => $survey->company,
            'survey' => $survey,
            'surveyAnswer' => $surveyAnswer,
            'surveyTargetUser' => $surveyTargetUser,
            'surveyAnswerDetails' => $surveyAnswerDetails,
        ]);
    }

    public function updateUser(Request $request, int $companyId, int $surveyId, int $surveyTargetUserId)
    {
        $company = $this->companyRepo->find($companyId);
        $survey = $this->surveyRepo->find($surveyId);

        $this->surveyRepo->updateTarget($request->all(),$surveyTargetUserId);

        // 二重送信防止
        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.companies.surveys.show', [$company, $survey])
            ->with('success', ['変更しました']);
    }

    public function destroyUser(int $companyId, int $surveyId, int $surveyTargetUserId)
    {
        $company = $this->companyRepo->find($companyId);
        $survey = $this->surveyRepo->find($surveyId);


        $surveyTargetUser = $this->surveyRepo->findTarget($surveyTargetUserId);

        $this->surveyRepo->deleteTarget($surveyTargetUser);

        return redirect()
            ->route('admin.companies.surveys.show', [$company, $survey])
            ->with('success', ['削除しました']);
    }

    /**
     * Webhookの受信処理
     */
    public function complete(Request $request)
    {
        $answer = $request->all();
        // JSONデータが正しくデコードされているか確認
        if ($answer === null) {
            return response()->json(['status' => 'error', 'message' => 'Invalid JSON data'], 400);
        }

        // 'custom_key'が存在するか確認
        $customKey = $answer['custom_key'] ?? null;
        if ($customKey === null) {
            return response()->json(['status' => 'error', 'message' => 'Custom key is missing'], 400);
        }
        // 設問登録
        $this->surveyRepo->storeQuestions($answer);
        // custom_keyを使ってsurvey_answersレコード（診断回答状況）を取得
        $surveyAnswer = $this->surveyRepo->findSurveyAnswerByCustomKey($customKey);

        if (! $surveyAnswer) {
            return response()->json(['status' => 'error', 'message' => 'Survey answer not found'], 404);
        }

        // 回答状況を回答済み、回答データを保存
        $result = $this->surveyRepo->completesSurvey($surveyAnswer, $answer);

        if ($result['status'] === 'error') {
            logger()->error('Webhook update failed', ['error_message' => $result['message']]);
            return response()->json(['status' => 'error', 'message' => $result['message']], 400);
        }

        // 成功した場合もログに残す
        logger()->info('Webhook processed successfully');

        return response()->json(['status' => 'success']);
    }
}
