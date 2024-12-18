<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    const SPECIFIED_CSV_HEADER = [
        '氏名',
        'メールアドレス',
        'チーム名・部署名（自由入力）',
        '役職名（自由入力）'
    ];
    const OPEN_CSV_HEADER = [
        'メールアドレス'
    ];

    const CSV_HEADER_COLUMN = [
        '氏名' => 'name',
        'メールアドレス' => 'email'
    ];

    const SPECIFIED_CSV_HEADER_VALIDATION = [
        'チーム名・部署名（自由入力）' => ['required', 'max: 30'],
        '役職名（自由入力）'  => ['required', 'max: 30'],
        '氏名' => ['required', 'max: 30'],
        'メールアドレス' => ['required', 'email']
    ];
    const OPEN_CSV_HEADER_VALIDATION = [
        'メールアドレス' => ['required', 'email']
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function surveyTargetUsers(): HasMany
    {
        return $this->hasMany(SurveyTargetUser::class);
    }

    public static function generatePassword(int $surveyId, string $uid): string
    {
        $surveyId = sprintf('%04d', $surveyId);

        return sprintf('%02d', $uid * 2) . $surveyId . sprintf('%01d', $uid * 3);
    }
}
