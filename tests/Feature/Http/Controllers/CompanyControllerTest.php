<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Company;
use App\Models\Admin;

class CompanyControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // 管理者を作成してログインさせる
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');
    }

    /**
     * @test
     * 企業情報を保存してリダイレクトされることを確認するテスト
     */
    public function it_should_store_a_new_company_and_redirect()
    {
        // CSRFトークン検証を無効化
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // POSTリクエストで送信するデータに必須フィールドを含める
        $response = $this->post(route('admin.companies.store'), [
            'name' => 'Test Company',
            'email' => 'test@example.com',
            'industry' => 'it', // 必須フィールド
            'contract_start_date' => '2024-01-01', // 必須フィールド
            'contract_end_date' => '2025-01-01', // 必須フィールド
        ]);

        // 正しいビューが返されるか確認
        $response->assertViewIs('admin.companies.store');
    }

    /**
     * @test
     * 企業情報を更新してリダイレクトされることを確認するテスト
     */
    public function it_should_update_a_company_and_redirect()
    {
        // CSRFトークン検証を無効化
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $company = Company::factory()->create();

        // PATCHリクエストで送信するデータに必須フィールドを含める
        $response = $this->patch(route('admin.companies.update', $company->id), [
            'name' => 'Updated Company Name',
            'industry' => 'finance', // 必須フィールド
            'contract_start_date' => '2024-01-01', // 必須フィールド
            'contract_end_date' => '2025-01-01', // 必須フィールド
        ]);

        // 正しいページへリダイレクトするか確認
        $response->assertRedirect(route('admin.companies.show', $company->id));

        // フラッシュメッセージの確認
        $response->assertSessionHas('success', ['企業情報が更新されました。']);
    }

    /**
     * @test
     * 企業を削除してリダイレクトされることを確認するテスト
     */
    public function it_should_delete_a_company_and_redirect()
    {
        // CSRFトークン検証を無効化
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $company = Company::factory()->create();

        $response = $this->delete(route('admin.companies.destroy', $company->id));

        // 正しいページへリダイレクトするか確認
        $response->assertRedirect(route('admin.companies.index'));

        // フラッシュメッセージの確認
        $response->assertSessionHas('success', ['企業を削除しました']);
    }
}
