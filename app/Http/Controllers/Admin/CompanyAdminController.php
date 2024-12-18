<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Admin;
use Illuminate\Http\Request;

class CompanyAdminController extends Controller
{

    public function assign($companyId)
    {
        $company = Company::findOrFail($companyId);

        // すでに担当している管理者
        $assignedAdmins = $company->admins;

        // 担当していないその他の管理者
        $otherAdmins = Admin::whereNotIn('id', $assignedAdmins->pluck('id'))->get();

        return view('admin.companies.admins.assign', compact('company', 'assignedAdmins', 'otherAdmins'));
    }

    public function store(Request $request, $companyId)
    {
        $company = Company::findOrFail($companyId);

        // フォームで選択された管理者のIDを取得（チェックが入ったもののみ）
        $adminIds = $request->input('admin_ids', []); // 選択された管理者IDリスト

        // `sync`メソッドでチェックされた管理者のみを割り当て（チェックが外された管理者は解除）
        $company->admins()->sync($adminIds);

        return redirect()->route('admin.companies.show', $companyId)->with('success', ['担当者を更新しました']);
    }


    public function destroy($companyId, $adminId)
    {
        $company = Company::findOrFail($companyId);
        $company->admins()->detach($adminId); // 担当者の解除

        return redirect()->route('admin.companies.admins.index', $companyId)->with('success', '担当者を解除しました');
    }
}
