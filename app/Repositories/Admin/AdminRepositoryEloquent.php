<?php

namespace App\Repositories\Admin;

use App\Models\Admin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Notifications\AdminCreatedNotification;

class AdminRepositoryEloquent implements AdminRepository
{
    public function __construct(Admin $admin)
    {
        $this->admin = $admin;
    }

    public function find(int $adminId): Admin
    {
        return $this->admin->findOrFail($adminId);
    }

    public function paginate(): LengthAwarePaginator
    {
        return $this->admin->orderBy('id', 'desc')->paginate(20);
    }

    public function store(array $params): void
    {
        $password = Str::random(8);
        $params['password'] = bcrypt($password);
        $admin = $this->admin->create($params);
        // 管理者に通知を送信
        $admin->notify(new AdminCreatedNotification($password));
    }

    public function update(Admin $admin, array $params): void
    {
        if (!empty($params['password'])) {
            $params['password'] = Hash::make($params['password']);
        } else {
            unset($params['password']);
        }
        $admin->fill($params)->save();
    }

    public function delete(Admin $admin): void
    {
        $admin->delete();
    }
}
