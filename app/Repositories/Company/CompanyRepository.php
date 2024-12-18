<?php

namespace App\Repositories\Company;

use App\Models\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CompanyRepository
{
    public function find(int $companyId): Company;

    public function paginateSurvey(int $companyId): LengthAwarePaginator;

    public function paginate(?string $q): LengthAwarePaginator;
    public function paginateAssignedCompanies(int $adminId, ?string $query): LengthAwarePaginator;

    public function store(array $params): void;

    public function update(Company $company, array $params): void;

    public function delete(Company $company): void;

    public function collectAll(): Collection;
}
