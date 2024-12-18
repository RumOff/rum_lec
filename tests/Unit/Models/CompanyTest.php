<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Company;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    protected $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::factory()->create();
    }

    /**
     * Company モデルが SoftDeletes を使用しているかテスト
     * @test
     */
    public function uses_soft_deletes()
    {
        $this->assertContains(SoftDeletes::class, class_uses(Company::class));
    }

    /**
     * フィラブル属性が正しく設定されているかテスト
     * @test
     */
    public function fillable_attributes()
    {
        $fillable = [
            'name',
            'industry',
            'contract_start_date',
            'contract_end_date'
        ];

        $this->assertEquals($fillable, $this->company->getFillable());
    }

    /**
     * 日付属性が正しく設定されているかテスト
     * @test
     */
    public function date_attributes()
    {
        $expectedDates = [
            'contract_start_date',
            'contract_end_date',
            'created_at',
            'updated_at'
        ];

        $actualDates = $this->company->getDates();

        // 期待される日付属性がすべて存在することを確認
        foreach ($expectedDates as $date) {
            $this->assertContains($date, $actualDates);
        }

        // 実際の日付属性の数が期待値と一致することを確認
        $this->assertCount(count($expectedDates), $actualDates);
    }

    /**
     * industries 静的プロパティが正しく定義されているかテスト
     * @test
     */
    public function industries_static_property()
    {
        $expectedIndustries = [
            'it' => 'IT',
            'finance' => '金融',
            'manufacturing' => '製造業',
        ];

        $this->assertEquals($expectedIndustries, Company::$industries);
    }

    /**
     * surveys リレーションが正しく機能しているかテスト
     * @test
     */
    public function has_many_surveys()
    {
        Survey::factory()->count(3)->create(['company_id' => $this->company->id]);

        $this->assertCount(3, $this->company->surveys);
        $this->assertInstanceOf(Survey::class, $this->company->surveys->first());
    }

    /**
     * users リレーションが正しく機能しているかテスト
     * @test
     */
    public function has_many_users()
    {
        User::factory()->count(3)->create(['company_id' => $this->company->id]);

        $this->assertCount(3, $this->company->users);
        $this->assertInstanceOf(User::class, $this->company->users->first());
    }

    /**
     * 日付属性が Carbon インスタンスにキャストされているかテスト
     * @test
     */
    public function date_casting()
    {
        $this->assertInstanceOf(Carbon::class, $this->company->contract_start_date);
        $this->assertInstanceOf(Carbon::class, $this->company->contract_end_date);
    }

    /**
     * Factory が正しく動作しているかテスト
     * @test
     */
    public function factory()
    {
        $company = Company::factory()->create();

        $this->assertNotNull($company->name);
        $this->assertContains($company->industry, ['it', 'finance', 'manufacturing']);
        $this->assertInstanceOf(Carbon::class, $company->contract_start_date);
        $this->assertInstanceOf(Carbon::class, $company->contract_end_date);
        $this->assertTrue($company->contract_start_date->isBefore($company->contract_end_date));
    }
}
