<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Company;
use App\Models\Survey;
use App\Models\SurveyDelivery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Admin;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

class SurveyDeliveryControllerTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');
    }

    /**
     * @test
     * 配信作成フォームが正しく表示されることを確認
     */
    public function create_shows_delivery_creation_form()
    {
        $company = Company::factory()->create();
        $survey = Survey::factory()->create([
            'company_id' => $company->id,
            'expires_at' => now()->addDays(7),
            'open_results' => false
        ]);

        $response = $this->get(route('admin.companies.surveys.survey-deliveries.create', [
            'companyId' => $company->id,
            'surveyId' => $survey->id
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('admin.survey-deliveries.create');
        $response->assertViewHas(['company', 'survey', 'emailTemplate']);
    }

    /**
     * @test
     * 期限切れのサーベイに対して配信作成画面にアクセスするとエラーになることを確認
     */
    public function create_returns_error_when_survey_expired()
    {
        $company = Company::factory()->create();
        $survey = Survey::factory()->create([
            'company_id' => $company->id,
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->get(route('admin.companies.surveys.survey-deliveries.create', [
            'companyId' => $company->id,
            'surveyId' => $survey->id
        ]));

        $response->assertRedirect();
        $response->assertSessionHasErrors('message');
    }

    /**
     * @test
     * 新規配信が正常に作成されることを確認
     */
    public function store_creates_new_delivery()
    {
        $admin = Admin::factory()->create();

        $company = Company::factory()->create();
        $survey = Survey::factory()->create([
            'company_id' => $company->id,
            'expires_at' => now()->addDays(7)
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->withHeaders([
                'X-CSRF-TOKEN' => csrf_token()
            ])
            ->post(route('admin.companies.surveys.survey-deliveries.store', [
                'companyId' => $company->id,
                'surveyId' => $survey->id
            ]), [
                'subject' => 'Test Subject',
                'body' => 'Test Body',
                'scheduled_sending_at' => now()->addDay()->format('Y-m-d H:i:s')
            ]);

        $response->assertStatus(200);
        $response->assertViewIs('admin.survey-deliveries.store');

        $this->assertDatabaseHas('survey_deliveries', [
            'targetable_id' => $survey->id,
            'targetable_type' => Survey::class,
            'subject' => 'Test Subject',
            'body' => 'Test Body'
        ]);
    }

    /**
     * @test
     * 未配信のサーベイ配信を削除できることを確認
     */
    public function destroy_deletes_undelivered_survey()
    {
        $company = Company::factory()->create();
        $survey = Survey::factory()->create(['company_id' => $company->id]);
        $delivery = SurveyDelivery::factory()->create([
            'targetable_id' => $survey->id,
            'targetable_type' => Survey::class,
            'completed_sending_at' => null,
            'scheduled_sending_at' => now()->addDay()
        ]);

        $response = $this->withoutMiddleware()->delete(route('admin.companies.surveys.survey-deliveries.destroy', [
            'companyId' => $company->id,
            'surveyId' => $survey->id,
            'surveyDeliveryId' => $delivery->id
        ]));

        $response->assertRedirect(route('admin.companies.surveys.show', [
            'companyId' => $company->id,
            'surveyId' => $survey->id
        ]));
        $response->assertSessionHas('success');

        // ソフトデリートの場合は deleted_at を確認
        $this->assertSoftDeleted('survey_deliveries', ['id' => $delivery->id]);
    }

    /**
     * @test
     * 配信済みのサーベイは削除できないことを確認
     */
    public function destroy_fails_for_delivered_survey()
    {
        $company = Company::factory()->create();
        $survey = Survey::factory()->create(['company_id' => $company->id]);
        $delivery = SurveyDelivery::factory()->create([
            'targetable_id' => $survey->id,
            'targetable_type' => Survey::class,
            'completed_sending_at' => now(),
            'scheduled_sending_at' => now()->addDay()
        ]);

        $response = $this->withoutMiddleware()->delete(route('admin.companies.surveys.survey-deliveries.destroy', [
            'companyId' => $company->id,
            'surveyId' => $survey->id,
            'surveyDeliveryId' => $delivery->id
        ]));

        $response->assertRedirect();
        $response->assertSessionHasErrors('message');
        $this->assertDatabaseHas('survey_deliveries', ['id' => $delivery->id]);
    }
}
