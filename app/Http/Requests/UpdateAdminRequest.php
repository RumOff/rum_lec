<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminRequest extends FormRequest
{
    public function rules(): array
    {
        $adminId = $this->route('adminId'); // 現在の管理者IDを取得
        return [
            'name' => ['required', 'string', 'max:20'],
            'email' => [
                'required',
                'email',
                'max:100',
                "unique:admins,email,{$adminId}", // 管理者IDを除外して一意性チェック
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'max:20',
                'regex:/^[a-zA-Z0-9!@#$%^&*()_+=-]+$/', // 半角英数字と記号のみ
            ],
            'is_superadmin' => ['boolean'], // Superadminフラグ
        ];
    }


    public function attributes(): array
    {
        return [
            'name' => '管理者名',
            'email' => 'メールアドレス',
            'password' => 'パスワード',
            'is_superadmin' => 'Super管理者権限',
        ];
    }

    protected function prepareForValidation()
    {
        if (!$this->filled('is_superadmin')) {
            $this->merge([
                'is_superadmin' => 0, // Superadminフラグがない場合は0に
            ]);
        }
    }
}
