<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSurveyRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => ['required', 'max:100'],
            'survey_type' => ['required', 'in:specified,open'],
            'starts_at' => ['required', 'date_format:Y-m-d', 'before_or_equal:expires_at'],
            'expires_at' => ['required', 'date_format:Y-m-d', 'after_or_equal:starts_at'],
            'form_url' => ['required', 'url', 'max:255'],
            'user_path' => ['required', 'string', 'max:255'],
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
            'user_path' => '対象者CSVパス',
            'form_password' => 'フォームパスワード',
        ];
    }
}
