<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveyDelivery;
use App\Repositories\Company\CompanyRepository;
use App\Repositories\Survey\SurveyRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class SurveyDeliveryController extends Controller
{
    public function __construct(CompanyRepository $companyRepo, SurveyRepository $surveyRepo)
    {
        $this->companyRepo = $companyRepo;
        $this->surveyRepo = $surveyRepo;
    }

    public function create(int $companyId, int $surveyId)
    {
        $company = $this->companyRepo->find($companyId);
        $survey = $this->surveyRepo->find($surveyId);

        if ($survey->expires_at->lt(now())) {
            return back()->withErrors([
                'message' => '締切日を過ぎているため配信予約ができません。',
            ])->withInput();
        }
        if ($survey->open_results) {
            return back()->withErrors([
                'message' => '診断結果が公開中のため配信予約ができません。',
            ])->withInput();
        }

        return view('admin.survey-deliveries.create')->with([
            'company' => $company,
            'survey' => $survey,
            'emailTemplate' => SurveyDelivery::getEmailTemplate(),
        ]);
    }

    public function store(Request $request, int $companyId, int $surveyId)
    {
        $company = $this->companyRepo->find($companyId);
        $survey = $this->surveyRepo->find($surveyId);

        if ($survey->expires_at->lt(now())) {
            return back()->withErrors([
                'message' => '締切日を過ぎているため配信予約ができません。',
            ])->withInput();
        }
        $this->surveyRepo->storeSurveyDelivery($survey, $request->all());

        // 二重送信防止
        $request->session()->regenerateToken();

        return view('admin.survey-deliveries.store')->with([
            'company' => $company,
            'survey' => $survey,
        ]);
    }

    public function show(int $companyId, int $surveyId, int $surveyDeliveryId)
    {
        $company = $this->companyRepo->find($companyId);
        $survey = $this->surveyRepo->find($surveyId);
        $surveyDelivery = $this->surveyRepo->findSurveyDelivery($surveyDeliveryId);

        return view('admin.survey-deliveries.show')->with([
            'company' => $company,
            'survey' => $survey,
            'surveyDelivery' => $surveyDelivery,
        ]);
    }

    public function destroy(int $companyId, int $surveyId, int $surveyDeliveryId)
    {
        $company = $this->companyRepo->find($companyId);
        $survey = $this->surveyRepo->find($surveyId);
        $surveyDelivery = $this->surveyRepo->findSurveyDelivery($surveyDeliveryId);

        if ($surveyDelivery->completed_sending_at) {
            return back()->withErrors([
                'message' => 'すでに配信済みのため配信停止することができません。',
            ]);
        }

        $this->surveyRepo->deleteSurveyDelivery($surveyDelivery);

        return redirect()
            ->route('admin.companies.surveys.show', [$company, $survey])
            ->with('success', ['配信を停止しました']);
    }

    public function getDefaultTemplate()
    {
        $emailTemplate = SurveyDelivery::getEmailTemplate(); // 通常のテンプレート
        return response()->json([
            'subject' => $emailTemplate['subject'],
            'body' => $emailTemplate['body'],
        ]);
    }
    public function getRemindTemplate()
    {
        $emailTemplate = SurveyDelivery::getEmailTemplateAtResend(); // リマインド用テンプレート
        return response()->json([
            'subject' => $emailTemplate['subject'],
            'body' => $emailTemplate['body'],
        ]);
    }

}
