<?php

namespace Tests\Unit\Http\Controllers\Admin;

use App\Http\Controllers\Admin\SurveyDeliveryController;
use App\Models\Company;
use App\Models\Survey;
use App\Repositories\Company\CompanyRepository;
use App\Repositories\Survey\SurveyRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Mockery;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
class SurveyDeliveryControllerTest extends TestCase
{
    protected $companyRepo;
    protected $surveyRepo;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->companyRepo = Mockery::mock(CompanyRepository::class);
        $this->surveyRepo = Mockery::mock(SurveyRepository::class);
        $this->controller = new SurveyDeliveryController(
            $this->companyRepo,
            $this->surveyRepo
        );
    }

    /**
     * @test
     * 期限切れのサーベイに対して配信作成時にエラーが返されることを確認
     */
    public function create_returns_error_for_expired_survey()
    {
        // モックの設定
        $company = Mockery::mock(Company::class);
        $survey = Mockery::mock(Survey::class);

        $survey->shouldReceive('getAttribute')->with('expires_at')
            ->andReturn(Carbon::yesterday());
        $survey->shouldReceive('getAttribute')->with('open_results')
            ->andReturn(false);

        $this->companyRepo->shouldReceive('find')->once()->andReturn($company);
        $this->surveyRepo->shouldReceive('find')->once()->andReturn($survey);

        $response = $this->controller->create(1, 1);

        $this->assertEquals(
            '締切日を過ぎているため配信予約ができません。',
            $response->getSession()->get('errors')->first()
        );
    }

    /**
     * @test
     * 結果公開中のサーベイに対して配信作成時にエラーが返されることを確認
     */
    public function create_returns_error_for_open_results()
    {
        // モックの設定
        $company = Mockery::mock(Company::class);
        $survey = Mockery::mock(Survey::class);

        $survey->shouldReceive('getAttribute')->with('expires_at')
            ->andReturn(Carbon::tomorrow());
        $survey->shouldReceive('getAttribute')->with('open_results')
            ->andReturn(true);

        $this->companyRepo->shouldReceive('find')->once()->andReturn($company);
        $this->surveyRepo->shouldReceive('find')->once()->andReturn($survey);

        $response = $this->controller->create(1, 1);

        $this->assertEquals(
            '診断結果が公開中のため配信予約ができません。',
            $response->getSession()->get('errors')->first()
        );
    }

      /**
     * @test
     * 配信の保存が正常に行われることを確認
     */
    public function store_creates_delivery_successfully()
    {
        // モックの設定
        $company = Mockery::mock(Company::class);
        $survey = Mockery::mock(Survey::class);

        $survey->shouldReceive('getAttribute')->with('expires_at')
            ->andReturn(Carbon::tomorrow());

        // セッションをモック
        $session = Mockery::mock('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $session->shouldReceive('regenerateToken')->once();

        // リクエストを作成
        $request = new Request();
        $request->setSession($session);
        $request->merge(['subject' => 'Test', 'body' => 'Test']);

        $this->companyRepo->shouldReceive('find')->once()->andReturn($company);
        $this->surveyRepo->shouldReceive('find')->once()->andReturn($survey);
        $this->surveyRepo->shouldReceive('storeSurveyDelivery')->once();

        $response = $this->controller->store($request, 1, 1);

        $this->assertEquals('admin.survey-deliveries.store', $response->getName());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
