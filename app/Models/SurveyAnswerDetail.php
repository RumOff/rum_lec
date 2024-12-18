<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyAnswerDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'survey_answer_id',
        'survey_question_id',
        'sort',
        'score',
        'text',
    ];

    protected $casts = [
        'score' => 'float',
    ];

    const SCORES = [
        1 => 1.0,
        2 => 2.0,
        3 => 3.0,
        4 => 4.0,
        5 => 5.0,
    ];

    public function surveyQuestion(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestion::class);
    }
}
