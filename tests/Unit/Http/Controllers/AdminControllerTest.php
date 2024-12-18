<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\Admin\AdminController;
use App\Repositories\Admin\AdminRepository;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $adminRepo;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        // リポジトリのモックを作成
        $this->adminRepo = Mockery::mock(AdminRepository::class);

        // コントローラーを初期化
        $this->controller = new AdminController($this->adminRepo);
    }

    /**
     * @test
     * 管理者一覧画面が正しいビューとデータを返すかを確認するテスト
     */
    public function it_should_return_admin_index_view_with_paginated_admins()
    {
        // モックデータの準備
        $admins = new LengthAwarePaginator([], 0, 10);

        // モック設定
        $this->adminRepo->shouldReceive('paginate')->once()->andReturn($admins);

        // アクションを実行
        $response = $this->controller->index();

        // ビューとデータの確認
        $this->assertEquals('admin.admins.index', $response->getName());
        $this->assertArrayHasKey('admins', $response->getData());
    }

    /**
     * @test
     * 管理者作成画面が正しいビューを返すかを確認するテスト
     */
    public function it_should_return_admin_create_view()
    {
        // アクションを実行
        $response = $this->controller->create();

        // ビューの確認
        $this->assertEquals('admin.admins.create', $response->getName());
    }

    /**
     * @test
     * 管理者編集画面が正しいビューとデータを返すかを確認するテスト
     */
    public function it_should_return_admin_edit_view_with_correct_data()
    {
        // モックデータの準備
        $adminId = 1;
        $admin = Admin::factory()->make([
            'id' => $adminId,
            'is_superadmin' => $this->faker->boolean, // is_superadminの追加
        ]);
        // モック設定: find メソッドの呼び出し
        $this->adminRepo->shouldReceive('find')->once()->with($adminId)->andReturn($admin);

        // アクションを実行
        $response = $this->controller->edit($adminId);

        // ビューとデータの確認
        $this->assertEquals('admin.admins.edit', $response->getName());
        $this->assertArrayHasKey('admin', $response->getData());
    }
}
