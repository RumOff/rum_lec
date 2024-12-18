<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Survey;
use App\Models\Company;
use App\Models\SurveyQuestion;
use App\Models\SurveyTargetUser;
use App\Models\SurveyAnswer;
use App\Models\SurveyDelivery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SurveyTest extends TestCase
{
    use RefreshDatabase;

    protected $survey;

    protected function setUp(): void
    {
        parent::setUp();
        $this->survey = Survey::factory()->create();
    }
    /**
     * Surveyモデルがソフトデリート機能を使用しているかテスト
     * @test
     */
    public function survey_uses_soft_deletes()
    {
        $this->assertContains(SoftDeletes::class, class_uses(Survey::class));
    }
    /**
     * フィラブル属性が正しく設定されているかテスト
     * @test
     */
    public function fillable_attributes()
    {
        $fillable = [
            'company_id',
            'form_url',
            'form_password',
            'title',
            'starts_at',
            'expires_at',
            'open_results',
        ];

        $this->assertEquals($fillable, $this->survey->getFillable());
    }
    /**
     * 日付フィールドが正しくキャストされているかテスト
     * @test
     */
    public function date_casts()
    {
        $this->assertIsObject($this->survey->starts_at);
        $this->assertIsObject($this->survey->expires_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $this->survey->starts_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $this->survey->expires_at);
    }
    /**
     * open_results属性が正しくブール値にキャストされているかテスト
     * @test
     */
    public function boolean_cast()
    {
        $this->assertIsBool($this->survey->open_results);
    }
    /**
     * Surveyモデルが正しくCompanyモデルに属しているかテスト
     * @test
     */
    public function survey_belongs_to_company()
    {
        $company = Company::factory()->create();
        $survey = Survey::factory()->create(['company_id' => $company->id]);

        $this->assertInstanceOf(Company::class, $survey->company);
        $this->assertEquals($company->id, $survey->company->id);
    }
    /**
     * SurveyモデルがSurveyAnswerモデルと正しく1対多の関係を持っているかテスト
     * @test
     */
    public function survey_has_many_survey_answers()
    {
        SurveyAnswer::factory()->count(3)->create(['survey_id' => $this->survey->id]);

        $this->assertCount(3, $this->survey->surveyAnswers);
        $this->assertInstanceOf(SurveyAnswer::class, $this->survey->surveyAnswers->first());
    }
    /**
     * SurveyモデルがSurveyTargetUserモデルと正しく1対多の関係を持っているかテスト
     * @test
     */
    public function survey_has_many_survey_target_users()
    {
        SurveyTargetUser::factory()->count(2)->create(['survey_id' => $this->survey->id]);

        $this->assertCount(2, $this->survey->surveyTargetUsers);
        $this->assertInstanceOf(SurveyTargetUser::class, $this->survey->surveyTargetUsers->first());
    }
    /**
     * SurveyモデルがSurveyQuestionモデルと正しく1対多の関係を持っているかテスト
     * @test
     */
    public function survey_has_many_survey_questions()
    {
        SurveyQuestion::factory()->count(5)->create(['survey_id' => $this->survey->id]);

        $this->assertCount(5, $this->survey->surveyQuestions);
        $this->assertInstanceOf(SurveyQuestion::class, $this->survey->surveyQuestions->first());
    }
    /**
     * SurveyモデルがSurveyDeliveryモデルと正しく1対多のポリモーフィック関係を持っているかテスト
     * @test
     */
    public function survey_has_many_survey_deliveries()
    {
        SurveyDelivery::factory()->count(2)->create([
            'targetable_id' => $this->survey->id,
            'targetable_type' => Survey::class,
        ]);

        $this->survey->refresh();

        $this->assertCount(2, $this->survey->surveyDeliveries);
        $this->assertInstanceOf(SurveyDelivery::class, $this->survey->surveyDeliveries->first());
    }
    /**
     * notExpiredスコープが正しく機能しているかテスト
     * @test
     */
    public function not_expired_scope()
    {
        // 期限切れのサーベイ
        Survey::factory()->create(['expires_at' => now()->subDay()]);

        // 期限内のサーベイ
        Survey::factory()->create(['expires_at' => now()->addDay()]);

        $notExpiredSurveys = Survey::notExpired()->get();

        $this->assertCount(2, $notExpiredSurveys); // 既存の1つと新しく作成した期限内のもの
        $this->assertTrue($notExpiredSurveys->every(function ($survey) {
            return $survey->expires_at >= today();
        }));
    }
}
