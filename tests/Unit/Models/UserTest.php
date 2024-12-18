<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\SurveyTargetUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * フィラブル属性が正しく設定されているかテスト
     * @test
     */
    public function fillable_attributes()
    {
        $fillable = [
            'company_id',
            'name',
            'email',
            'password',
        ];

        $this->assertEquals($fillable, $this->user->getFillable());
    }

    /**
     * hidden属性が正しく設定されているかテスト
     * @test
     */
    public function hidden_attributes()
    {
        $hidden = [
            'password',
            'remember_token',
        ];

        $this->assertEquals($hidden, $this->user->getHidden());
    }

    /**
     * UserモデルがCompanyモデルに属しているかテスト
     * @test
     */
    public function belongs_to_company()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);

        $this->assertInstanceOf(Company::class, $user->company);
        $this->assertEquals($company->id, $user->company->id);
    }

    /**
     * Userモデルが複数のSurveyTargetUserを持つかテスト
     * @test
     */
    public function has_many_survey_target_users()
    {
        SurveyTargetUser::factory()->count(3)->create(['user_id' => $this->user->id]);

        $this->assertInstanceOf(Collection::class, $this->user->surveyTargetUsers);
        $this->assertCount(3, $this->user->surveyTargetUsers);
    }

    /**
     * CSVヘッダー定数が正しく定義されているかテスト
     * @test
     */
    public function csv_header_constants()
    {
        $this->assertEquals(['氏名', 'メールアドレス', 'チーム名・部署名（自由入力）', '役職名（自由入力）'], User::SPECIFIED_CSV_HEADER);
        $this->assertEquals(['メールアドレス'], User::OPEN_CSV_HEADER);
        $this->assertEquals(['氏名' => 'name', 'メールアドレス' => 'email'], User::CSV_HEADER_COLUMN);
    }

    /**
     * CSV検証ルールが正しく定義されているかテスト
     * @test
     */
    public function csv_validation_rules()
    {
        $specifiedRules = [
            'チーム名・部署名（自由入力）' => ['required', 'max: 30'],
            '役職名（自由入力）'  => ['required', 'max: 30'],
            '氏名' => ['required', 'max: 30'],
            'メールアドレス' => ['required', 'email']
        ];
        $this->assertEquals($specifiedRules, User::SPECIFIED_CSV_HEADER_VALIDATION);

        $openRules = ['メールアドレス' => ['required', 'email']];
        $this->assertEquals($openRules, User::OPEN_CSV_HEADER_VALIDATION);
    }

    /**
     * generatePassword メソッドが正しく機能するかテスト
     * @test
     */
    public function generate_password()
    {
        $surveyId = 1234;
        $uid = 5;

        $expectedPassword = '10123415';
        $this->assertEquals($expectedPassword, User::generatePassword($surveyId, $uid));
    }
}
