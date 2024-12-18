<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware; // CSRFを無効化
use Tests\TestCase;
use App\Models\Admin;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware; // これを追加

    /**
     * @test
     * 管理者を作成してリダイレクトされることを確認するテスト
     */
    public function it_should_store_a_new_admin_and_redirect()
    {
        // POSTリクエストで管理者データを送信
        $response = $this->post(route('admin.admins.store'), [
            'name' => 'Admin Name',
            'email' => 'admin@example.com',
            'password' => 'secret',
        ]);

        // 正しいページへリダイレクトするか確認
        $response->assertRedirect(route('admin.admins.index'));

        // セッションにフラッシュメッセージが保存されているか確認
        $response->assertSessionHas('success', ['新規管理者を登録しました。']);
    }
    /**
     * @test
     * 管理者を更新してリダイレクトされることを確認するテスト
     */
    public function it_should_update_an_admin_and_redirect()
    {
        $admin = Admin::factory()->create();

        // PUTリクエストで管理者データを更新
        $response = $this->patch(route('admin.admins.update', $admin->id), [
            'name' => 'Updated Admin Name',
            'email' => $admin->email,
        ]);

        // 正しいページへリダイレクトするか確認
        $response->assertRedirect(route('admin.admins.index'));

        // セッションにフラッシュメッセージが保存されているか確認
        $response->assertSessionHas('success', ['変更しました']);
    }
    /**
     * @test
     * 管理者を削除してリダイレクトされることを確認するテスト
     */
    public function it_should_delete_an_admin_and_redirect()
    {
        $admin = Admin::factory()->create();

        // DELETEリクエストで管理者を削除
        $response = $this->delete(route('admin.admins.destroy', $admin->id));

        // 正しいページへリダイレクトするか確認
        $response->assertRedirect(route('admin.admins.index'));

        // セッションにフラッシュメッセージが保存されているか確認
        $response->assertSessionHas('success', ['管理者を削除しました']);
    }
}
