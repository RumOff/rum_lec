<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::enforceMorphMap([
            'survey' => \App\Models\Survey::class,
            'user' => \App\Models\User::class,
        ]);
        // 言語ファイルのパスを追加
        $this->loadTranslationsFrom(resource_path('lang'), 'messages');
        // 本番ではhttpsを強制
        if ($this->app->environment() == 'production') {
            URL::forceRootUrl('https://oseru.new-one.co.jp');
            URL::forceScheme('https');
        }

        // userとaminでcookieを使い分けてログイン/ログアウトでの誤作動を抑止
        if (request()->is('admin*')) {
            config(['session.cookie' => config('session.cookie_admin')]);
        }

        // デフォルトのpagination.viewの設定
        Paginator::defaultView('pagination.default');

        View::composer('*', function ($view) {
            // URLに「/admin」が含まれている場合のみ、$currentAdminをビューに渡す
            if (Request::is('admin*')) {
                $view->with('currentAdmin', Auth::guard('admin')->user());
            }
        });
    }
}
