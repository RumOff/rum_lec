<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware; // CSRFを無効化
use Tests\TestCase;
use App\Models\Admin;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 正しい認証情報でログインが成功し、リダイレクトされるかを確認
     */
    public function it_should_login_and_redirect_on_successful_authentication()
    {
        Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        // CSRFトークン検証を無効化
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // POSTリクエストでログイン
        $response = $this->post(route('admin.login'), [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        // 正しいページへリダイレクトするか確認
        $response->assertRedirect(route('admin.companies.index')); // ルート名を修正
    }

    /**
     * @test
     * ログアウト処理が正しく行われ、リダイレクトされるかを確認
     */
    public function it_should_logout_and_redirect_to_login()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // ログアウトリクエスト
        $response = $this->get(route('admin.logout')); // 管理者ログアウト用ルートを指定

        // 正しいページへリダイレクトするか確認
        $response->assertRedirect(route('admin.login')); // ルート名を修正
    }
}
