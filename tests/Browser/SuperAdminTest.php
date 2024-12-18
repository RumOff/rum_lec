<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Admin;
use App\Models\Company;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;

class SuperAdminTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function superadmin_can_login_and_access_dashboard()
    {
        $this->browse(function (Browser $browser) {
            $superAdmin = Admin::factory()->create([
                'email' => 'superadmin@example.com',
                'password' => bcrypt('password'),
                'is_superadmin' => true,
            ]);

            $browser->visit('/admin/login')
                // ->screenshot('01_login_page')  // ログイン画面
                ->type('email', $superAdmin->email)
                ->type('password', 'password')
                // ->screenshot('02_filled_login_form')  // 入力済みログインフォーム
                ->press('ログイン')
                ->assertPathIs('/admin/companies')
                // ->screenshot('03_companies_dashboard')  // ダッシュボード
                ->assertSee('企業情報一覧')
                ->logout();
        });
    }

    /** @test */
    public function superadmin_can_manage_admins()
    {
        $this->browse(function (Browser $browser) {
            $newAdminEmail = 'testadmin@example.com';
            $superAdmin = Admin::factory()->create([
                'email' => 'superadmin@example.com',
                'password' => bcrypt('password'),
                'is_superadmin' => true,
            ]);

            // 管理者の作成
            $browser->loginAs($superAdmin, 'admin')
                ->visit('/admin/companies')
                ->visit('/admin/admins/create')
                // ->screenshot('04_admin_create_form')  // 管理者作成フォーム
                ->type('name', 'Test Admin')
                ->type('email', $newAdminEmail)
                // ->screenshot('05_filled_admin_form')  // 入力済み管理者フォーム
                ->press('登録')
                ->assertSee('登録しました')
                // ->screenshot('06_admin_created')  // 作成完了画面
                ->assertPathIs('/admin/admins');

            // 作成された管理者IDの取得
            $createdAdminId = Admin::where('email', $newAdminEmail)->value('id');

            // 管理者の削除
            $browser->visit("/admin/admins/{$createdAdminId}/edit")
                // ->screenshot('07_admin_edit_page')  // 管理者編集画面
                ->click("#admin-delete-button-{$createdAdminId}")
                ->assertDialogOpened('削除してよろしいですか？')
                ->acceptDialog()
                ->assertPathIs('/admin/admins')
                // ->screenshot('08_admin_deleted')  // 削除完了画面
                ->assertSee('管理者を削除しました')
                ->logout();
        });
    }

    /** @test */
    public function superadmin_can_manage_companies()
    {
        $this->browse(function (Browser $browser) {
            $superAdmin = Admin::factory()->create([
                'email' => 'superadmin@example.com',
                'password' => bcrypt('password'),
                'is_superadmin' => true,
            ]);

            $companyName = 'Example株式会社';
            $industry = 'it';

            // 企業の登録
            $browser->loginAs($superAdmin, 'admin')
                ->visit('/admin/companies')
                ->visit('/admin/companies/create')
                // ->screenshot('09_company_create_form')  // 企業作成フォーム
                ->type('name', $companyName)
                ->select('industry', $industry)
                ->keys('input[name="contract_start_date"]', '2023', '{tab}', '01', '01')
                ->keys('input[name="contract_end_date"]', '2024', '{tab}', '01', '01')
                // ->screenshot('10_filled_company_form')  // 入力済み企業フォーム
                ->press('登録')
                ->assertSee('登録が完了しました')
                // ->screenshot('11_company_created')  // 作成完了画面
                ->assertPathIs('/admin/companies');

            // 作成した企業のIDを取得して削除処理を確認
            $companyId = Company::where('name', $companyName)->value('id');

            // 企業の削除
            $browser->visit("/admin/companies/{$companyId}/edit")
                // ->screenshot('12_company_edit_page')  // 企業編集画面
                ->click("#company-delete-button-{$companyId}")
                ->assertDialogOpened('削除してよろしいですか？')
                ->acceptDialog()
                ->assertPathIs('/admin/companies')
                // ->screenshot('13_company_deleted')  // 削除完了画面
                ->assertSee('企業を削除しました')
                ->logout();
        });
    }
}
