<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'industry',
        'contract_start_date',
        'contract_end_date'
    ];

    protected $dates = [
        'contract_start_date',
        'contract_end_date'
    ];

    // 業種の選択肢を定義
    public static $industries = [
        'it' => 'IT',
        'finance' => '金融',
        'manufacturing' => '製造業',
        // 他の業種も追加してください
    ];
    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'company_admins');
    }
    public function surveys(): HasMany
    {
        return $this->hasMany(Survey::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
