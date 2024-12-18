<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\Admin\SurveyController;
use App\Models\Company;
use App\Models\Survey;
use App\Repositories\Company\CompanyRepository;
use App\Repositories\Survey\SurveyRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class SurveyControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $companyRepo;
    protected $surveyRepo;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        // モックの作成
        $this->companyRepo = Mockery::mock(CompanyRepository::class);
        $this->surveyRepo = Mockery::mock(SurveyRepository::class);
        $this->userRepo = Mockery::mock(UserRepository::class);

        // コントローラを初期化
        $this->controller = new SurveyController(
            $this->companyRepo,
            $this->surveyRepo,
            $this->userRepo
        );
    }

    /**
     * @test
     * 企業のサーベイ作成画面が正しく表示されるか確認
     */
    public function it_should_return_create_survey_view_with_company()
    {
        // モックの設定
        $companyId = 1;
        $company = Mockery::mock(Company::class);
        $this->companyRepo->shouldReceive('find')->once()->with($companyId)->andReturn($company);

        // アクションの実行
        $response = $this->controller->create($companyId);

        // ビューの確認
        $this->assertEquals('admin.surveys.create', $response->getName());
        $this->assertArrayHasKey('company', $response->getData());
    }

    /**
     * @test
     * サーベイの編集画面が正しく表示されるか確認
     */
    public function it_should_return_edit_survey_view_with_company_and_survey()
    {
        $companyId = 1;
        $surveyId = 1;
        $company = Mockery::mock(Company::class);
        $survey = Mockery::mock(Survey::class); // stdClassからSurveyへ

        $this->companyRepo->shouldReceive('find')->once()->with($companyId)->andReturn($company);
        $this->surveyRepo->shouldReceive('find')->once()->with($surveyId)->andReturn($survey);

        $response = $this->controller->edit($companyId, $surveyId);

        $this->assertEquals('admin.surveys.edit', $response->getName());
        $this->assertArrayHasKey('company', $response->getData());
        $this->assertArrayHasKey('survey', $response->getData());
    }

    /**
     * @test
     * サーベイが正しく削除され、リダイレクトされるか確認
     */
    public function it_should_delete_a_survey_and_redirect()
    {
        $companyId = 1;
        $surveyId = 1;
        $company = Mockery::mock(Company::class);
        $company->shouldReceive('getRouteKey')->andReturn($companyId);
        // Mock返すオブジェクトをSurveyに変更する
        $survey = Mockery::mock(Survey::class); // stdClassからSurveyへ

        $this->companyRepo->shouldReceive('find')->once()->with($companyId)->andReturn($company);
        $this->surveyRepo->shouldReceive('find')->once()->with($surveyId)->andReturn($survey);
        $this->surveyRepo->shouldReceive('deleteSurvey')->once()->with($survey);

        $response = $this->controller->destroy($companyId, $surveyId);

        $this->assertEquals(route('admin.companies.show', $company), $response->getTargetUrl());
    }
}
