<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\SurveyDelivery;
use App\Models\Survey;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SurveyDeliveryTest extends TestCase
{
    use RefreshDatabase;

    protected $surveyDelivery;

    protected function setUp(): void
    {
        parent::setUp();
        $this->surveyDelivery = SurveyDelivery::factory()->create();
    }
    /**
     * フィラブル属性が正しく設定されているかテスト
     * @test
     */
    public function fillable_attributes()
    {
        $fillable = [
            'targetable_id',
            'targetable_type',
            'job_id',
            'subject',
            'body',
            'scheduled_sending_at',
            'started_sending_at',
            'completed_sending_at',
            'sending_count',
        ];

        $this->assertEquals($fillable, $this->surveyDelivery->getFillable());
    }
    /**
     * ソフトデリート機能が使用されているかテスト
     * @test
     */
    public function survey_delivery_uses_soft_deletes()
    {
        $this->assertContains(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses(SurveyDelivery::class));
    }
    /**
     * ポリモーフィックリレーションが正しく機能しているかテスト
     * @test
     */
    public function survey_delivery_belongs_to_targetable()
    {
        $survey = Survey::factory()->create();
        $surveyDelivery = SurveyDelivery::factory()->create([
            'targetable_id' => $survey->id,
            'targetable_type' => Survey::class,
        ]);

        $this->assertInstanceOf(Survey::class, $surveyDelivery->targetable);
        $this->assertEquals($survey->id, $surveyDelivery->targetable->id);
    }
    /**
     * 日付フィールドが正しくキャストされているかテスト
     * @test
     */
    public function date_casts()
    {
        $dates = ['scheduled_sending_at', 'started_sending_at', 'completed_sending_at'];
        foreach ($dates as $date) {
            $this->assertIsObject($this->surveyDelivery->$date);
            $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $this->surveyDelivery->$date);
        }
    }
    /**
     * job_id属性が正しく設定されているかテスト
     * @test
     */
    public function job_id_attribute()
    {
        $surveyDelivery = SurveyDelivery::factory()->create(['job_id' => 1]);
        $this->assertIsInt($surveyDelivery->job_id);
    }
    /**
     * モデルの属性が正しくキャストされているかテスト
     * @test
     */
    public function casts()
    {
        $expected = [
            'id' => 'int',
            'scheduled_sending_at' => 'datetime',
            'started_sending_at' => 'datetime',
            'completed_sending_at' => 'datetime',
            'sending_count' => 'integer',
            'deleted_at' => 'datetime',
        ];

        $actual = $this->surveyDelivery->getCasts();

        // キーの存在と値の型をチェック
        foreach ($expected as $key => $type) {
            $this->assertArrayHasKey($key, $actual);
            $this->assertEquals($type, $actual[$key]);
        }
    }
    /**
     * getEmailTemplate メソッドが正しく機能しているかテスト
     * @test
     */
    public function get_email_template()
    {
        $template = SurveyDelivery::getEmailTemplate();
        $this->assertIsArray($template);
        $this->assertArrayHasKey('subject', $template);
        $this->assertArrayHasKey('body', $template);
        $this->assertStringContainsString('[[企業名]]', $template['subject']);
        $this->assertStringContainsString('[[診断名]]', $template['subject']);
        $this->assertStringContainsString('[[フォームURL]]', $template['body']);
    }
    /**
     * getEmailTemplateAtResend メソッドが正しく機能しているかテスト
     * @test
     */
    public function get_email_template_at_resend()
    {
        $template = SurveyDelivery::getEmailTemplateAtResend();
        $this->assertIsArray($template);
        $this->assertArrayHasKey('subject', $template);
        $this->assertArrayHasKey('body', $template);
        $this->assertStringContainsString('【リマインド】', $template['subject']);
        $this->assertStringContainsString('サーベイ未受診の皆さま', $template['body']);
    }
    /**
     * swapEmailSubjectTemplate メソッドが正しく機能しているかテスト
     * @test
     */
    public function swap_email_subject_template()
    {
        $subject = '【[[企業名]]様 [[診断名]]】推せる職場診断受診のお願い';
        $result = SurveyDelivery::swapEmailSubjectTemplate($subject, 'テスト企業', 'テスト診断');
        $this->assertEquals('【テスト企業様 テスト診断】推せる職場診断受診のお願い', $result);
    }
    /**
     * swapEmailBodyTemplate メソッドが正しく機能しているかテスト
     * @test
     */
    public function swap_email_body_template()
    {
        $body = 'アンケート　：[[フォームURL]]?survey=[[診断ID]]&answer=[[カスタムキー]]';
        $replacements = [
            'formUrl' => 'http://example.com',
            'surveyId' => '123',
            'customKey' => 'abc123',
        ];
        $result = SurveyDelivery::swapEmailBodyTemplate($body, $replacements);
        $this->assertEquals('アンケート　：http://example.com?survey=123&answer=abc123', $result);
    }
    /**
     * survey リレーションシップが正しく定義されているかテスト
     * @test
     */
    public function survey_relationship()
    {
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $this->surveyDelivery->survey());
    }
    /**
     * targetable ポリモーフィックリレーションが正しく定義されているかテスト
     * @test
     */
    public function targetable_relationship()
    {
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphTo::class, $this->surveyDelivery->targetable());
    }
}
