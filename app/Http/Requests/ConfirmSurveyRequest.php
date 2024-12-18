<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConfirmSurveyRequest extends FormRequest
{
    public function rules()
    {
        $today = now()->startOfDay();

        return [
            'title' => ['required', 'max:100'],
            'survey_type' => ['required', 'in:specified,open'],
            'starts_at' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:' . $today->format('Y-m-d'),
                'before_or_equal:expires_at'
            ],
            'expires_at' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:starts_at'
            ],
            'form_url' => ['required', 'url', 'max:255'],
            'csv_users' => ['required', 'max:2048', 'file', 'mimes:csv,txt'],
            'form_password' => ['nullable', 'string', 'min:4', 'max:255'],
        ];
    }

    public function attributes()
    {
        return [
            'title' => '診断名',
            'survey_type' => 'サーベイ種別',
            'starts_at' => '診断実施開始日',
            'expires_at' => '診断実施終了日',
            'form_url' => 'フォームURL',
            'csv_users' => '対象者CSV',
            'form_password' => 'フォームパスワード',
        ];
    }
}
