<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyAnswer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'survey_id',
        'survey_target_user_id',
        'custom_key',
        'completes_at',
    ];

    protected $casts = [
        'completes_at' => 'datetime',
    ];

    const CSV_HEADER = [
        'ID',
        '設問内容',
        '種類',
        '回答種類'
    ];

    const CSV_HEADER_COLUMN = [
        'ID' => 'id',
        '設問内容' => 'team',
        '種類' => 'post',
        '回答種類' => 'post'
    ];

    public function surveyAnswerDetails(): HasMany
    {
        return $this->hasMany(SurveyAnswerDetail::class);
    }

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function surveyTargetUser(): BelongsTo
    {
        return $this->belongsTo(SurveyTargetUser::class);
    }
}
