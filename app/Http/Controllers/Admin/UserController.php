<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserSearchRequest;
use App\Repositories\Company\CompanyRepository;
use App\Repositories\Survey\SurveyRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(
        SurveyRepository $surveyRepository,
        UserRepository $userRepo
    ) {
        $this->surveyRepo = $surveyRepository;
        $this->userRepo = $userRepo;
    }

    public function searchs(CompanyRepository $companyRepo, Request $request)
    {
        $users = $this
            ->userRepo
            ->searchByNameOrEmail(
                $request->input('company_id'),
                $request->input('_q')
            )
            ->appends($request->all());

        return view('admin.users.search')->with([
            'companies' => $companyRepo->collectAll(),
            'users' => $users
        ]);
    }

    public function surveys(int $userId)
    {
        $user = $this->userRepo->find($userId);
        $surveyTargetUsers = $this->surveyRepo->collectSurveyTargetUserBy($userId);

        return view('admin.users.surveys')->with([
            'user' => $user,
            'surveyTargetUsers' => $surveyTargetUsers
        ]);
    }

    public function resultsShow(int $userId, int $surveyAnswerId)
    {
        $user = $this->userRepo->find($userId);
        $surveyAnswer = $this->surveyAnswerRepo->getCompletedSurveyAnswer($surveyAnswerId);

        if (!$this->surveyAnswerRepo->showlableResults($user, $surveyAnswer->survey_id)) {
            return back()->withErrors(['この結果は閲覧できません。']);
        }

        return Inertia::render('CarrierCraftResult', [
            'results' => $this
                ->surveyAnswerRepo
                ->formatResults($user, $surveyAnswer->survey_id)
        ]);
    }
}
