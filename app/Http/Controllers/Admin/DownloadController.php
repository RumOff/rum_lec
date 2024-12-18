<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DownloadByPeriodRequest;
use App\Repositories\Company\CompanyRepository;
use App\Repositories\Survey\SurveyRepository;
use App\Services\Csv\Download as CsvDownloadService;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function __construct(
        CompanyRepository $companyRepo,
        SurveyRepository $surveyRepo,
        CsvDownloadService $csvService
    ) {
        $this->companyRepo = $companyRepo;
        $this->surveyRepo = $surveyRepo;
        $this->csvService = $csvService;
    }

    public function index()
    {
        return view('admin.downloads')->with([
            'companies' => $this->companyRepo->collectAll(),
            'surveys' => $this->surveyRepo->collectAll()
        ]);
    }

    public function byCompany(Request $request)
    {
        $callback = $this->csvService->downloadByCompany($request->input('company_id'));
        $filename = sprintf('company-%s-%s.csv', $request->input('company_id'), now()->format('Ymd_His'));
        $header = $this->csvService->getHeader();

        return response()->streamDownload($callback, $filename, $header);
    }

    public function byPeriod(DownloadByPeriodRequest $request)
    {
        $callback = $this->csvService
            ->downloadbyPeriod($request->input('starts_at'), $request->input('expires_at'));
        $filename = sprintf('period-%s-%s.csv', $request->input('starts_at'), $request->input('expires_at'));
        $header = $this->csvService->getHeader();

        return response()->streamDownload($callback, $filename, $header);
    }

    public function bySurvey(Request $request)
    {
        $callback = $this->csvService
            ->downloadBySurvey($request->input('survey_id'));
        $filename = sprintf('survey-%s-%s.csv', $request->input('survey_id'), now()->format('Ymd_His'));
        $header = $this->csvService->getHeader();

        return response()->streamDownload($callback, $filename, $header);
    }
}
