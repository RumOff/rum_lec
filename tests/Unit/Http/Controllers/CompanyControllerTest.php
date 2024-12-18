<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\Admin\CompanyController;
use App\Repositories\Company\CompanyRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Company;
use App\Models\Admin;

class CompanyControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $companyRepo;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        // リポジトリのモックを作成
        $this->companyRepo = Mockery::mock(CompanyRepository::class);

        // コントローラーを初期化
        $this->controller = new CompanyController($this->companyRepo);
    }

    /**
     * @test
     * 企業一覧画面が正しいビューとデータを返すかを確認するテスト
     */
    public function it_should_return_company_index_view_with_paginated_companies()
    {
        // superadminか一般管理者かによって、どちらのメソッドを呼び出すかを分岐
        $admin = Admin::factory()->create(['is_superadmin' => true]); // superadminの場合
        $this->actingAs($admin, 'admin'); // superadminでログインを模倣

        // モックデータを準備
        $companies = new LengthAwarePaginator([], 0, 10);

        // superadminの場合は `paginate` メソッドを期待
        $this->companyRepo->shouldReceive('paginate')->once()->andReturn($companies);

        // リクエストのモック
        $request = Request::create('/admin/companies', 'GET');

        // アクションを実行
        $response = $this->controller->index($request);

        // ビューとデータの確認
        $this->assertEquals('admin.companies.index', $response->getName());
        $this->assertArrayHasKey('companies', $response->getData());

        // 一般管理者の場合も同様にテスト
        $admin = Admin::factory()->create(['is_superadmin' => false]); // 一般管理者の場合
        $this->actingAs($admin, 'admin'); // 一般管理者でログインを模倣

        // `paginateAssignedCompanies`メソッドの呼び出しを期待
        $this->companyRepo->shouldReceive('paginateAssignedCompanies')
            ->with($admin->id, $request->input('q'))
            ->once()
            ->andReturn($companies);

        // アクションを再実行
        $response = $this->controller->index($request);

        // ビューとデータの確認
        $this->assertEquals('admin.companies.index', $response->getName());
        $this->assertArrayHasKey('companies', $response->getData());
    }

    /**
     * @test
     * 企業作成画面が正しいビューを返すかを確認するテスト
     */
    public function it_should_return_company_create_view()
    {
        // 認証済みのsuperadminユーザーを設定
        $admin = Admin::factory()->create(['is_superadmin' => true]);
        $this->actingAs($admin, 'admin'); // 管理者でログインを模倣

        // アクションを実行
        $response = $this->controller->create();

        // ビューの確認
        $this->assertEquals('admin.companies.create', $response->getName());
    }
    /**
     * @test
     * 企業情報編集画面が正しいビューとデータを返すかを確認するテスト
     */
    public function it_should_return_company_edit_view_with_correct_data()
    {
        // モックデータの準備
        $companyId = 1;
        $company = Company::factory()->make();

        // モック設定: find メソッドの呼び出し
        $this->companyRepo->shouldReceive('find')->once()->with($companyId)->andReturn($company);

        // アクションを実行
        $response = $this->controller->edit($companyId);

        // ビューとデータの確認
        $this->assertEquals('admin.companies.edit', $response->getName());
        $this->assertArrayHasKey('company', $response->getData());
    }
}
