<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepository
{
    public function find(int $userId): User;

    public function searchByNameOrEmail(?int $companyId, ?string $nameOrEmail): LengthAwarePaginator;
}
