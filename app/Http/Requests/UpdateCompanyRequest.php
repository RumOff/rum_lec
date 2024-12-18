<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Company;
class UpdateCompanyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'industry' => 'required|string|in:' . implode(',', array_keys(Company::$industries)),
            'contract_start_date' => 'required|date',
            'contract_end_date' => 'required|date|after:contract_start_date',
        ];
    }
    public function attributes()
    {
        return [
            'name' => '企業名',
            'industry' => '業種',
            'contract_start_date' => '契約開始日',
            'contract_end_date' => '契約終了日',
        ];
    }
}
