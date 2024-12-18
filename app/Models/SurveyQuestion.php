<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'sort',
        'major_category',
        'medium_category',
        'minor_category',
        'question_text',
        'answer_options',
    ];

    protected $casts = [
        'sort' => 'integer',
    ];
    const CSV_HEADER = [
        '設問No',
        '大分類',
        '中分類',
        '小分類（因子）',
        '設問文',
        '回答選択肢'
    ];

    const CSV_HEADER_COLUMN = [
        '設問No' => 'sort',
        '大分類' => 'major_category',
        '中分類' => 'medium_category',
        '小分類（因子）' => 'minor_category',
        '設問文' => 'question_text',
        '回答選択肢' => 'answer_options',
    ];

    const CSV_HEADER_VALIDATION = [
        '設問No' => ['required', 'numeric', 'integer'],
        '大分類' => ['nullable', 'max: 30'],
        '中分類' => ['nullable', 'max: 30'],
        '小分類（因子）'  => ['nullable', 'max: 30'],
        '設問文' => ['required', 'max: 200'],
        '回答選択肢'  => ['nullable', 'max: 30'],
    ];
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}
