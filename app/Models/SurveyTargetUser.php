<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyTargetUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'survey_id',
        'user_id',
        'team',
        'post',
    ];

    const CSV_HEADER_COLUMN = [
        'ID' => 'id',
        'チーム名・部署名（自由入力）' => 'team',
        '役職名（自由入力）' => 'post'
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function surveyAnswer(): HasOne
    {
        return $this->hasOne(SurveyAnswer::class);
    }
}
