<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\SurveyAnswer;
use App\Models\Survey;
use App\Models\SurveyTargetUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyAnswerTest extends TestCase
{
    use RefreshDatabase;

    protected $surveyAnswer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->surveyAnswer = SurveyAnswer::factory()->create();
    }

    /**
     * フィラブル属性が正しく設定されているかテスト
     * @test
     */
    public function fillable_attributes()
    {
        $fillable = [
            'survey_id',
            'survey_target_user_id',
            'custom_key',
            'completes_at',
        ];

        $this->assertEquals($fillable, $this->surveyAnswer->getFillable());
    }
    /**
     * ソフトデリートが使用されているかテスト
     * @test
     */
    public function uses_soft_deletes()
    {
        $this->assertContains(SoftDeletes::class, class_uses(SurveyAnswer::class));
    }
    /**
     * 日付キャストが正しく設定されているかテスト
     * @test
     */
    public function date_casts()
    {
        $this->surveyAnswer->completes_at = now();
        $this->surveyAnswer->save();

        $this->assertIsObject($this->surveyAnswer->completes_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $this->surveyAnswer->completes_at);
    }

    /**
     * SurveyAnswerモデルが正しくSurveyモデルに属しているかテスト
     * @test
     */
    public function belongs_to_survey()
    {
        $survey = Survey::factory()->create();
        $surveyAnswer = SurveyAnswer::factory()->create(['survey_id' => $survey->id]);

        $this->assertInstanceOf(Survey::class, $surveyAnswer->survey);
        $this->assertEquals($survey->id, $surveyAnswer->survey->id);
    }

    /**
     * SurveyAnswerモデルが正しくSurveyTargetUserモデルに属しているかテスト
     * @test
     */
    public function belongs_to_survey_target_user()
    {
        $surveyTargetUser = SurveyTargetUser::factory()->create();
        $surveyAnswer = SurveyAnswer::factory()->create(['survey_target_user_id' => $surveyTargetUser->id]);

        $this->assertInstanceOf(SurveyTargetUser::class, $surveyAnswer->surveyTargetUser);
        $this->assertEquals($surveyTargetUser->id, $surveyAnswer->surveyTargetUser->id);
    }
    /**
     * SurveyAnswerモデルが複数のSurveyAnswerDetailを持つかテスト
     * @test
     */
    public function has_many_survey_answer_details()
    {
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $this->surveyAnswer->surveyAnswerDetails());
    }
    /**
     * custom_key属性が一意であることをテスト
     * @test
     */
    public function custom_key_is_unique()
    {
        $customKey = 'unique_key_123';
        SurveyAnswer::factory()->create(['custom_key' => $customKey]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        SurveyAnswer::factory()->create(['custom_key' => $customKey]);
    }
    /**
     * CSV_HEADER定数が正しく定義されているかテスト
     * @test
     */
    public function csv_header_constants()
    {
        $this->assertEquals([
            'ID',
            '設問内容',
            '種類',
            '回答種類'
        ], SurveyAnswer::CSV_HEADER);

        $this->assertEquals([
            'ID' => 'id',
            '設問内容' => 'team',
            '種類' => 'post',
            '回答種類' => 'post'
        ], SurveyAnswer::CSV_HEADER_COLUMN);
    }
}
