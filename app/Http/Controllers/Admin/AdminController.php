<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\AdminRepository;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateAdminRequest;
use Illuminate\Database\QueryException;

class AdminController extends Controller
{
    public function __construct(AdminRepository $adminRepo)
    {
        $this->adminRepo = $adminRepo;
    }

    public function index()
    {
        return view('admin.admins.index')->with([
            'admins' => $this->adminRepo->paginate()
        ]);
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        try {
            $this->adminRepo->store($request->all());
            return redirect()->route('admin.admins.index')->with('success', ['新規管理者を登録しました。']);
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->back()
                    ->withInput($request->except('password'))
                    ->withErrors(['email' => 'このメールアドレスは既に登録されています。']);
            }
            throw $e;
        }
    }

    public function edit(int $adminId)
    {
        return view('admin.admins.edit')->with([
            'admin' => $this->adminRepo->find($adminId)
        ]);
    }

    public function update(UpdateAdminRequest $request, int $adminId)
    {
        $admin = $this->adminRepo->find($adminId);
        $this->adminRepo->update($admin, $request->all());

        return redirect()->route('admin.admins.index')->with('success', ['変更しました']);
    }

    public function destroy(int $adminId)
    {
        $admin = $this->adminRepo->find($adminId);
        $this->adminRepo->delete($admin);

        return redirect()
            ->route('admin.admins.index')
            ->with('success', ['管理者を削除しました']);
    }
}
