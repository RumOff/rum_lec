<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Survey extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'form_url',
        'form_password',
        'title',
        'starts_at',
        'expires_at',
        'open_results',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'expires_at' => 'date',
        'open_results' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function surveyAnswers(): HasMany
    {
        return $this->hasMany(SurveyAnswer::class);
    }

    public function surveyTargetUsers(): HasMany
    {
        return $this->hasMany(SurveyTargetUser::class);
    }

    public function surveyQuestions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class);
    }

    // public function surveyDeliveries(): MorphMany
    // {
    //     return $this->morphMany(SurveyDelivery::class, 'targetable');
    // }
    public function surveyDeliveries()
    {
        return $this->hasMany(SurveyDelivery::class, 'targetable_id')->where('targetable_type', Survey::class);
    }

    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>=', today());
    }
}
