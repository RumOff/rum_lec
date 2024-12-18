<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\SurveyQuestion;
use App\Models\Survey;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SurveyQuestionTest extends TestCase
{
    use RefreshDatabase;

    protected $surveyQuestion;

    protected function setUp(): void
    {
        parent::setUp();
        $this->surveyQuestion = SurveyQuestion::factory()->create();
    }

    /**
     * フィラブル属性が正しく設定されているかテスト
     * @test
     */
    public function fillable_attributes()
    {
        $fillable = [
            'survey_id',
            'sort',
            'major_category',
            'medium_category',
            'minor_category',
            'question_text',
            'answer_options',
        ];

        $this->assertEquals($fillable, $this->surveyQuestion->getFillable());
    }

    /**
     * キャストが正しく設定されているかテスト
     * @test
     */
    public function casts()
    {
        $expected = [
            'id' => 'int',
            'sort' => 'integer',
        ];

        $this->assertEquals($expected, $this->surveyQuestion->getCasts());
    }

    /**
     * SurveyQuestionモデルが正しくSurveyモデルに属しているかテスト
     * @test
     */
    public function belongs_to_survey()
    {
        $survey = Survey::factory()->create();
        $surveyQuestion = SurveyQuestion::factory()->create(['survey_id' => $survey->id]);

        $this->assertInstanceOf(Survey::class, $surveyQuestion->survey);
        $this->assertEquals($survey->id, $surveyQuestion->survey->id);
    }

    /**
     * answer_options属性のテスト
     * @test
     */
    public function answer_options()
    {
        $options = ['option1', 'option2', 'option3'];
        $this->surveyQuestion->answer_options = json_encode($options);
        $this->surveyQuestion->save();

        $this->surveyQuestion->refresh();
        $this->assertEquals($options, json_decode($this->surveyQuestion->answer_options, true));
        $this->assertIsString($this->surveyQuestion->answer_options);
    }

    /**
     * CSVヘッダー定数が正しく定義されているかテスト
     * @test
     */
    public function csv_header_constants()
    {
        $this->assertEquals([
            '設問No',
            '大分類',
            '中分類',
            '小分類（因子）',
            '設問文',
            '回答選択肢'
        ], SurveyQuestion::CSV_HEADER);

        $this->assertEquals([
            '設問No' => 'sort',
            '大分類' => 'major_category',
            '中分類' => 'medium_category',
            '小分類（因子）' => 'minor_category',
            '設問文' => 'question_text',
            '回答選択肢' => 'answer_options',
        ], SurveyQuestion::CSV_HEADER_COLUMN);

        $this->assertEquals([
            '設問No' => ['required', 'numeric', 'integer'],
            '大分類' => ['nullable', 'max: 30'],
            '中分類' => ['nullable', 'max: 30'],
            '小分類（因子）'  => ['nullable', 'max: 30'],
            '設問文' => ['required', 'max: 200'],
            '回答選択肢'  => ['nullable', 'max: 30'],
        ], SurveyQuestion::CSV_HEADER_VALIDATION);
    }
}
