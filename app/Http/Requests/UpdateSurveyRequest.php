<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSurveyRequest extends FormRequest
{
    public function rules()
    {
        $today = now()->startOfDay();

        return [
            'title' => ['required', 'max:100'],
            'starts_at' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:today',
                'before_or_equal:expires_at'
            ],
            'expires_at' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:starts_at'
            ],
            // 'open_results' => ['required', 'in:0,1'],
        ];
    }

    public function attributes()
    {
        return [
            'title' => '診断名',
            'starts_at' => '診断実施開始日',
            'expires_at' => '診断実施終了日',
            // 'open_results' => '診断結果の閲覧',
        ];
    }

    protected function prepareForValidation()
    {
        if (! $this->filled('open_results')) {
            $this->merge([
                'open_results' => 0
            ]);
        }
    }
}
