<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\SurveyTargetUser;
use App\Models\Survey;
use App\Models\User;
use App\Models\SurveyAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyTargetUserTest extends TestCase
{
    use RefreshDatabase;

    protected $surveyTargetUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->surveyTargetUser = SurveyTargetUser::factory()->create();
    }

    /**
     * フィラブル属性が正しく設定されているかテスト
     * @test
     */
    public function fillable_attributes()
    {
        $fillable = [
            'survey_id',
            'user_id',
            'team',
            'post',
        ];

        $this->assertEquals($fillable, $this->surveyTargetUser->getFillable());
    }

    /**
     * ソフトデリートが使用されているかテスト
     * @test
     */
    public function uses_soft_deletes()
    {
        $this->assertContains(SoftDeletes::class, class_uses(SurveyTargetUser::class));
    }

    /**
     * SurveyTargetUserモデルが正しくSurveyモデルに属しているかテスト
     * @test
     */
    public function belongs_to_survey()
    {
        $this->assertInstanceOf(Survey::class, $this->surveyTargetUser->survey);
    }

    /**
     * SurveyTargetUserモデルが正しくUserモデルに属しているかテスト
     * @test
     */
    public function belongs_to_user()
    {
        $this->assertInstanceOf(User::class, $this->surveyTargetUser->user);
    }

    /**
     * SurveyTargetUserモデルが1つのSurveyAnswerを持つかテスト
     * @test
     */
    public function has_one_survey_answer()
    {
        $surveyAnswer = SurveyAnswer::factory()->create([
            'survey_target_user_id' => $this->surveyTargetUser->id
        ]);

        $this->assertInstanceOf(SurveyAnswer::class, $this->surveyTargetUser->surveyAnswer);
        $this->assertEquals($surveyAnswer->id, $this->surveyTargetUser->surveyAnswer->id);
    }

    /**
     * CSV_HEADER_COLUMN定数が正しく定義されているかテスト
     * @test
     */
    public function csv_header_column_constant()
    {
        $expected = [
            'ID' => 'id',
            'チーム名・部署名（自由入力）' => 'team',
            '役職名（自由入力）' => 'post'
        ];

        $this->assertEquals($expected, SurveyTargetUser::CSV_HEADER_COLUMN);
    }

    /**
     * ファクトリーのroleRaterメソッドが正しく機能するかテスト
     * @test
     */
    public function factory_role_rater()
    {
        $rater = SurveyTargetUser::factory()->roleRater()->create();
        $this->assertContains($rater->post, ['上司', 'トレーナー']);
    }

    /**
     * ファクトリーのroleRatedメソッドが正しく機能するかテスト
     * @test
     */
    public function factory_role_rated()
    {
        $rated = SurveyTargetUser::factory()->roleRated()->create();
        $this->assertContains($rated->post, ['新卒新人', '中途新人']);
    }
}
