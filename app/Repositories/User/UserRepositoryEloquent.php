<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\DatabaseManager;

class UserRepositoryEloquent implements UserRepository
{
    public function __construct(
        User $user,
        DatabaseManager $db
    ) {
        $this->user = $user;
        $this->db = $db;
    }

    public function find(int $userId): User
    {
        return $this->user->with('company')->findOrFail($userId);
    }

    public function searchByNameOrEmail(?int $companyId, ?string $nameOrEmail): LengthAwarePaginator
    {
        return $this->user
            ->with('company')
            ->when(
                $companyId,
                fn ($query) => $query->where('company_id', $companyId)
            )
            ->when(
                $nameOrEmail,
                fn ($query) => $query->where(
                    fn ($query) => $query
                        ->where('name', 'like', "%${nameOrEmail}%")
                        ->orWhere('email', 'like', "%${nameOrEmail}%")
                )
            )
            ->orderBy('id', 'DESC')
            ->paginate(10);
    }
}
