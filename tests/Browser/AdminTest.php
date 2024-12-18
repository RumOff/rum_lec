<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Admin;
use App\Models\Company;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class AdminTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function admin_can_login_and_access_dashboard()
    {
        $this->browse(function (Browser $browser) {
            // adminのテストデータを作成
            $admin = Admin::factory()->create([
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'is_superadmin' => false,
            ]);

            // adminでログイン
            $browser->visit('/admin/login')
                ->type('email', $admin->email)
                ->type('password', 'password')
                ->press('ログイン')
                ->assertPathIs('/admin/companies')
                ->assertSee('企業情報一覧')
                ->logout();
        });
    }

    /** @test */
    public function admin_work_flow()
    {
        $this->browse(function (Browser $browser) {
            // Superadminの作成と割り当てられた企業の設定
            $admin = Admin::factory()->create([
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'is_superadmin' => true,
            ]);
            $company = Company::factory()->create();

            // CSVフォーマットに従ったダミーCSVファイルの作成
            Storage::fake('local');
            $csvContent = "氏名,チーム名・部署名（自由入力）,役職名（自由入力）,メールアドレス\nUser1,部署A,役職A,user1@example.com\nUser2,部署B,役職B,user2@example.com";
            $csvPath = Storage::disk('local')->put('sample-user.csv', $csvContent);

            // テスト用のファイルパスを取得
            $csvFilePath = Storage::disk('local')->path('sample-user.csv');
            $csvFile = new UploadedFile($csvFilePath, 'sample-user.csv', 'text/csv', null, true);

            $today = now();
            $nextMonth = now()->addMonth();

            // 診断の登録プロセスをテスト
            $browser->loginAs($admin, 'admin')
                ->visit("/admin/companies/{$company->id}/surveys/create")
                ->type('title', 'Test Survey')
                ->select('survey_type', 'specified')
                ->keys('#starts_at', $today->format('m'), $today->format('d'), $today->format('Y'))
                ->keys('#expires_at', $nextMonth->format('m'), $nextMonth->format('d'), $nextMonth->format('Y'))
                ->attach('csv_users', $csvFilePath)
                ->type('form_url', 'https://example.com/survey-form')
                ->type('form_password', 'survey123')
                ->press('確認')
                ->assertPathIs("/admin/companies/{$company->id}/surveys/confirm")
                ->press('登録')
                ->assertPathIs("/admin/companies/{$company->id}/surveys")
                ->assertSee('登録完了')
                ->clickLink('企業詳細へ')
                ->logout();
        });
    }
}
