<?php

namespace App\Repositories\Admin;

use App\Models\Admin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AdminRepository
{
    public function find(int $adminId): Admin;

    public function paginate(): LengthAwarePaginator;

    public function store(array $params): void;

    public function update(Admin $admin, array $params): void;

    public function delete(Admin $admin): void;
}
