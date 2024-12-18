<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\SurveyAnswerDetail;
use App\Models\SurveyQuestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyAnswerDetailTest extends TestCase
{
    use RefreshDatabase;

    protected $surveyAnswerDetail;

    protected function setUp(): void
    {
        parent::setUp();
        $this->surveyAnswerDetail = SurveyAnswerDetail::factory()->create();
    }

    /**
     * SurveyAnswerDetail モデルが SoftDeletes を使用しているかテスト
     * @test
     */
    public function uses_soft_deletes()
    {
        $this->assertContains(SoftDeletes::class, class_uses(SurveyAnswerDetail::class));
    }

    /**
     * フィラブル属性が正しく設定されているかテスト
     * @test
     */
    public function fillable_attributes()
    {
        $fillable = [
            'survey_answer_id',
            'survey_question_id',
            'sort',
            'score',
            'text',
        ];

        $this->assertEquals($fillable, $this->surveyAnswerDetail->getFillable());
    }

    /**
     * キャストが正しく設定されているかテスト
     * @test
     */
    public function casts()
    {
        $expectedCasts = [
            'score' => 'float',
            'id' => 'int',
            'deleted_at' => 'datetime',
        ];

        $actualCasts = $this->surveyAnswerDetail->getCasts();

        // 期待されるキャストがすべて存在し、正しい型であることを確認
        foreach ($expectedCasts as $attribute => $type) {
            $this->assertArrayHasKey($attribute, $actualCasts);
            $this->assertEquals($type, $actualCasts[$attribute]);
        }
    }

    /**
     * SCORES 定数が正しく定義されているかテスト
     * @test
     */
    public function scores_constant()
    {
        $expectedScores = [
            1 => 1.0,
            2 => 2.0,
            3 => 3.0,
            4 => 4.0,
            5 => 5.0,
        ];

        $this->assertEquals($expectedScores, SurveyAnswerDetail::SCORES);
    }

    /**
     * surveyQuestion リレーションが正しく機能しているかテスト
     * @test
     */
    public function belongs_to_survey_question()
    {
        $surveyQuestion = SurveyQuestion::factory()->create();
        $surveyAnswerDetail = SurveyAnswerDetail::factory()->create(['survey_question_id' => $surveyQuestion->id]);

        $this->assertInstanceOf(SurveyQuestion::class, $surveyAnswerDetail->surveyQuestion);
        $this->assertEquals($surveyQuestion->id, $surveyAnswerDetail->surveyQuestion->id);
    }

    /**
     * score 属性が float にキャストされているかテスト
     * @test
     */
    public function score_is_cast_to_float()
    {
        $surveyAnswerDetail = SurveyAnswerDetail::factory()->score()->create();

        $this->assertIsFloat($surveyAnswerDetail->score);
        $this->assertNull($surveyAnswerDetail->text);
        $this->assertContains($surveyAnswerDetail->score, SurveyAnswerDetail::SCORES);
    }

    /**
     * text 属性のみが設定されているかテスト
     * @test
     */
    public function text_answer()
    {
        $surveyAnswerDetail = SurveyAnswerDetail::factory()->text()->create();

        $this->assertNull($surveyAnswerDetail->score);
        $this->assertIsString($surveyAnswerDetail->text);
        $this->assertNotEmpty($surveyAnswerDetail->text);
    }

    /**
     * score と text が同時に設定されていないことをテスト
     * @test
     */
    public function score_and_text_are_mutually_exclusive()
    {
        $scoreAnswer = SurveyAnswerDetail::factory()->score()->create();
        $textAnswer = SurveyAnswerDetail::factory()->text()->create();

        $this->assertTrue(
            ($scoreAnswer->score !== null && $scoreAnswer->text === null) ||
                ($scoreAnswer->score === null && $scoreAnswer->text !== null)
        );

        $this->assertTrue(
            ($textAnswer->score === null && $textAnswer->text !== null) ||
                ($textAnswer->score !== null && $textAnswer->text === null)
        );
    }
}
