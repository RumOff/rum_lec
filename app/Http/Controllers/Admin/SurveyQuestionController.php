<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Company\CompanyRepository;
use App\Repositories\Survey\SurveyRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class SurveyQuestionController extends Controller
{
    public function __construct(CompanyRepository $companyRepo, SurveyRepository $surveyRepo)
    {
        $this->companyRepo = $companyRepo;
        $this->surveyRepo = $surveyRepo;
    }

    public function edit(int $companyId, int $surveyId)
    {
        $company = $this->companyRepo->find($companyId);
        $survey = $this->surveyRepo->find($surveyId);
        $questions = $this->surveyRepo->collectQuestion($surveyId);
        // $canEditQuestion = $this->surveyRepo->canEditQuestion($surveyId);

        return view('admin.survey-questions.edit')->with([
            'company' => $company,
            'survey' => $survey,
            'questions' => $questions,
            // 'canEditQuestion' => $canEditQuestion,
        ]);
    }

    // public function update(Request $request, int $companyId, int $surveyId)
    // {
    //     $company = $this->companyRepo->find($companyId);
    //     $survey = $this->surveyRepo->find($surveyId);

    //     if (! $this->surveyRepo->canEditQuestion($surveyId)) {
    //         return back()
    //             ->withErrors('サーベイが開始しているため設問リストを編集することはできません。');
    //     }

    //     $this->surveyRepo->updateQuestions($survey, $request->input('questions'));

    //     return redirect()
    //         ->route('admin.companies.surveys.show', [$company, $survey])
    //         ->with('success', ['変更しました']);
    // }
}
