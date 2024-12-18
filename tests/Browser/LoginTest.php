<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function admin_can_login()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit('/admin/login')
                ->type('email', $admin->email)
                ->type('password', "password")
                ->press('ログイン');

            if (Auth::guard('admin')->attempt(['email' => $admin->email, 'password' => 'password'])) {
                echo "Auth successful";
            } else {
                echo "Auth failed";
            }

            $this->assertAuthenticatedAs($admin, 'admin');

            $browser->assertPathIs('/admin/companies');
        });
    }

    /**
     * @test
     */
    public function admin_cannot_login_with_invalid_credentials()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/login')
                ->type('email', 'wrong@example.com')
                ->type('password', 'wrongpassword')
                ->press('ログイン')
                ->assertSee('入力情報ではログインできません。');
        });
    }
}
