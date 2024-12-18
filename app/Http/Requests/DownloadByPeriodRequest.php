<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class DownloadByPeriodRequest extends FormRequest
{
    public function rules()
    {
        return [
            'starts_at' => ['required', 'date_format:Y-m-d'],
            'expires_at' => [
                'required',
                'date_format:Y-m-d',
                'after:starts_at',
                function ($attribute, $value, $fail) {
                    $startsAt = Carbon::parse($this->input('starts_at'));
                    $expiresAt = Carbon::parse($value);

                    if ($startsAt->diffInDays($expiresAt) > 120) {
                        $fail('回答期間は120日以内になるように設定してください。');
                    }
                },

            ],
        ];
    }

    public function attributes()
    {
        return [
            'starts_at' => '回答期間',
            'expires_at' => '回答期間',
        ];
    }
}
