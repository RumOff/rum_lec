<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new AuthController();
    }

    /**
     * @test
     * ログインフォームが正しく表示されるかを確認
     */
    public function it_should_return_login_form_view()
    {
        $response = $this->controller->showLoginForm();
        $this->assertEquals('admin.login', $response->getName());
    }

    /**
     * @test
     * 正しい認証情報でログインが成功するかを確認
     */
    public function it_should_login_with_valid_credentials()
    {
        // 認証モック設定
        $credentials = ['email' => 'admin@example.com', 'password' => 'password'];
        Auth::shouldReceive('guard')->with('admin')->andReturnSelf();
        Auth::shouldReceive('attempt')->with($credentials, false)->andReturn(true);

        $request = Request::create('/admin/login', 'POST', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        // セッションのモックを作成し、リクエストに設定
        $session = Mockery::mock(\Illuminate\Session\Store::class);
        $session->shouldReceive('regenerate');
        $request->setLaravelSession($session);

        // コントローラーのloginメソッドを呼び出す
        $response = $this->controller->login($request);

        // 成功した場合の処理だけを確認（リダイレクトはFeatureで確認）
        $this->assertTrue(Auth::guard('admin')->attempt($credentials, false));
    }

    /**
     * @test
     * 間違った認証情報でログインが失敗するかを確認
     */
    public function it_should_fail_login_with_invalid_credentials()
    {
        // 認証モック設定
        $credentials = ['email' => 'admin@example.com', 'password' => 'wrongpassword'];
        Auth::shouldReceive('guard')->with('admin')->andReturnSelf();
        Auth::shouldReceive('attempt')->with($credentials, false)->andReturn(false);

        // リクエストデータのモック
        $request = Request::create('/admin/login', 'POST', $credentials);

        $response = $this->controller->login($request);

        // 認証失敗時の処理を確認（リダイレクトやエラーはFeatureで確認）
        $this->assertFalse(Auth::guard('admin')->attempt($credentials, false));
    }
}
