<?php

namespace App\Repositories\Company;

use App\Models\Company;
use App\Models\Survey;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Collection;

class CompanyRepositoryEloquent implements CompanyRepository
{
    public function __construct(
        Company $company,
        Survey $survey,
        DatabaseManager $db
    ) {
        $this->company = $company;
        $this->survey = $survey;
        $this->db = $db;
    }

    public function find(int $companyId): Company
    {
        return $this->company->findOrFail($companyId);
    }

    public function paginateSurvey(int $companyId): LengthAwarePaginator
    {
        return $this
            ->survey
            ->where('company_id', $companyId)
            ->orderBy('id', 'desc')
            ->paginate(20);
    }

    public function paginate(?string $q): LengthAwarePaginator
    {
        return $this
            ->company
            ->when($q, fn($query) => $query->where('name', 'like', "%${q}%"))
            ->orderBy('id', 'desc')
            ->paginate(20);
    }
    public function paginateAssignedCompanies(int $adminId, ?string $query = null): LengthAwarePaginator
    {
        return $this
            ->company
            ->whereHas('admins', function ($query) use ($adminId) {
                $query->where('admin_id', $adminId);
            })
            ->where('name', 'like', '%' . $query . '%')
            ->paginate(20);
    }

    public function store(array $params): void
    {
        $this->company->create($params);
    }

    public function update(Company $company, array $params): void
    {
        $company->fill($params)->save();
    }

    public function delete(Company $company): void
    {
        $company->delete();
    }

    public function collectAll(): Collection
    {
        return $this->company->orderBy('id', 'desc')->limit(30)->get();
    }
}
