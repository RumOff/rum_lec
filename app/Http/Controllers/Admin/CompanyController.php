<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Company\CompanyRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;

class CompanyController extends Controller
{
    public function __construct(CompanyRepository $companyRepo)
    {
        $this->companyRepo = $companyRepo;
    }

    public function index(Request $request)
    {
        $admin = auth()->user(); // ログイン中のユーザー情報を取得

        // superadminなら全企業、一般管理者なら担当企業のみを取得
        if ($admin->is_superadmin) {
            $companies = $this->companyRepo->paginate($request->input('q'));
        } else {
            $companies = $this->companyRepo->paginateAssignedCompanies($admin->id, $request->input('q'));
        }
        return view('admin.companies.index')->with([
            'companies' => $companies
        ]);
    }

    public function create()
    {
        return view('admin.companies.create');
    }

    public function store(StoreCompanyRequest $request)
    {
        $this->companyRepo->store($request->all());

        // 二重送信防止
        $request->session()->regenerateToken();

        return view('admin.companies.store');
    }

    public function show(int $companyId)
    {
        $company = $this->companyRepo->find($companyId);
        $surveys = $this->companyRepo->paginateSurvey($companyId);

        return view('admin.companies.show')->with([
            'company' => $company,
            'surveys' => $surveys,
            'companyAdmins' => $company->admins,
        ]);
    }

    public function edit(int $companyId)
    {
        $company = $this->companyRepo->find($companyId);

        return view('admin.companies.edit')->with([
            'company' => $company
        ]);
    }

    public function update(UpdateCompanyRequest $request, int $companyId)
    {
        $company = $this->companyRepo->find($companyId);

        // 更新処理
        $this->companyRepo->update($company, $request->all());

        // リダイレクト
        return redirect()->route('admin.companies.show', $companyId)
            ->with('success', ['企業情報が更新されました。']);
    }

    public function destroy(int $companyId)
    {
        $company = $this->companyRepo->find($companyId);

        $this->companyRepo->delete($company);

        return redirect()
            ->route('admin.companies.index')
            ->with('success', ['企業を削除しました']);
    }
}
