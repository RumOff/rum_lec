<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
    }

    /**
     * Admin モデルが Authenticatable を継承しているかテスト
     *
     * @test
     */
    public function admin_extends_authenticatable()
    {
        $this->assertInstanceOf(Authenticatable::class, $this->admin);
    }

    /**
     * guard 名が正しく設定されているかテスト
     *
     * @test
     */
    public function guard_name()
    {
        $reflection = new \ReflectionClass($this->admin);
        $property = $reflection->getProperty('guard');
        $property->setAccessible(true);
        $guardName = $property->getValue($this->admin);

        $this->assertEquals('admin', $guardName);
    }

    /**
     * フィラブル属性が正しく設定されているかテスト
     *
     * @test
     */
    public function fillable_attributes()
    {
        $fillable = [
            'name',
            'email',
            'password',
            'is_superadmin',
        ];

        $this->assertEquals($fillable, $this->admin->getFillable());
    }

    /**
     * hidden 属性が正しく設定されているかテスト
     *
     * @test
     */
    public function hidden_attributes()
    {
        $hidden = [
            'password',
            'remember_token',
        ];

        $this->assertEquals($hidden, $this->admin->getHidden());
    }

    /**
     * Factory を使用して Admin モデルが正しく作成できるかテスト
     *
     * @test
     */
    public function admin_factory()
    {
        $admin = Admin::factory()->create();
        $this->assertInstanceOf(Admin::class, $admin);
        $this->assertNotNull($admin->name);
        $this->assertNotNull($admin->email);
        $this->assertNotNull($admin->password);
    }

    /**
     * パスワードがハッシュ化されているかテスト
     *
     * @test
     */
    public function password_is_hashed()
    {
        $plainPassword = 'password123';
        $admin = Admin::factory()->create(['password' => bcrypt($plainPassword)]);

        $this->assertNotEquals($plainPassword, $admin->password);
        $this->assertTrue(Hash::check($plainPassword, $admin->password));
    }
}
