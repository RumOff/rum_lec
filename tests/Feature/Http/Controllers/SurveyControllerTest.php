<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Admin;
use App\Models\Company;
use App\Models\Survey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Services\CsvUploadService;
use App\Services\Csv\Upload;

use Mockery;

class SurveyControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // フィーチャーテストでの認証追加
        $admin = Admin::factory()->create(); // 管理者を作成
        $this->actingAs($admin, 'admin'); // 管理者として認証された状態にする
    }
    /**
     * @test
     * サーベイの登録処理の確認画面まで
     */
    public function confirmation_view_with_valid_csv()
    {
        $this->withoutExceptionHandling();
        $company = Company::factory()->create();

        $csvData = [
            'メールアドレス',
            'test@example.com',
            'another@example.com',
        ];
        $csvFile = UploadedFile::fake()->createWithContent('test.csv', implode("\n", $csvData));

        $response = $this->post(route('admin.companies.surveys.confirm', $company->id), [
            'title' => 'Test Survey',
            'survey_type' => 'open',
            'starts_at' => now()->addDay()->format('Y-m-d'),
            'expires_at' => now()->addWeek()->format('Y-m-d'),
            'form_url' => 'https://example.com',
            'csv_users' => $csvFile
        ]);
        $response->assertViewIs('admin.surveys.confirm');
    }
    /**
     * @test
     * サーベイ/ユーザーの登録処理処理が正常に完了するか確認
     */
    public function confirm_and_store_survey()
    {
        $this->withoutExceptionHandling();

        $company = Company::factory()->create();

        $csvData = [
            'メールアドレス',
            'test@example.com',
            'another@example.com',
        ];
        $csvFile = UploadedFile::fake()->createWithContent('test.csv', implode("\n", $csvData));

        // 確認画面への遷移
        $confirmResponse = $this->post(route('admin.companies.surveys.confirm', $company->id), [
            'title' => 'Test Survey',
            'survey_type' => 'open',
            'starts_at' => now()->addDay()->format('Y-m-d'),
            'expires_at' => now()->addWeek()->format('Y-m-d'),
            'form_url' => 'https://example.com',
            'csv_users' => $csvFile
        ]);
        $confirmResponse->assertViewIs('admin.surveys.confirm');
        $confirmResponse->assertViewHas('userPath');

        // 登録処理
        $storeResponse = $this->post(route('admin.companies.surveys.store', $company->id), [
            'title' => 'Test Survey',
            'survey_type' => 'open',
            'starts_at' => now()->addDay()->format('Y-m-d'),
            'expires_at' => now()->addWeek()->format('Y-m-d'),
            'form_url' => 'https://example.com',
            'user_path' => $confirmResponse->viewData('userPath'),
            'csv_users' => $csvFile
        ]);

        $storeResponse->assertStatus(200);
        $storeResponse->assertViewIs('admin.surveys.store');
        $this->assertDatabaseHas('surveys', [
            'company_id' => $company->id,
            'title' => 'Test Survey',
            'starts_at' => now()->addDay()->format('Y-m-d'),
            'expires_at' => now()->addWeek()->format('Y-m-d'),
            'form_url' => 'https://example.com',
        ]);
    }


    /**
     * @test
     * サーベイの編集処理が正常に完了し、リダイレクトされるか確認
     */
    public function it_should_update_a_survey_and_redirect()
    {
        $company = Company::factory()->create();
        $survey = Survey::factory()->create(['company_id' => $company->id]);

        // CSVファイルをアップロード
        $csv = UploadedFile::fake()->createWithContent('updated_survey.csv', "氏名,メールアドレス\nJane Doe,updateduser@example.com\n");

        // CSRFトークンの検証を無効化する
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $response = $this->patch(route('admin.companies.surveys.update', [$company->id, $survey->id]), [
            'title' => 'Updated Survey',
            'form_url' => 'http://example.com',
            'form_password' => 'newpassword123',
            'starts_at' => now()->format('Y-m-d'),
            'expires_at' => now()->addDays(10)->format('Y-m-d'),
            'survey_type' => 'specified',
            'csv_users' => $csv, // アップロードされたファイル
        ]);

        $response->assertRedirect(route('admin.companies.surveys.show', [$company->id, $survey->id]));
        $response->assertSessionHas('success', ['変更しました']);
    }

    /**
     * @test
     * サーベイが正しく削除され、リダイレクトされるか確認
     */
    public function it_should_delete_a_survey_and_redirect()
    {
        $company = Company::factory()->create();
        $survey = Survey::factory()->create(['company_id' => $company->id]);

        // CSRFトークンの検証を無効化する
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $response = $this->delete(route('admin.companies.surveys.destroy', [$company->id, $survey->id]));

        $response->assertRedirect(route('admin.companies.show', $company->id));
        $response->assertSessionHas('success', ['診断を削除しました']);
    }
}
