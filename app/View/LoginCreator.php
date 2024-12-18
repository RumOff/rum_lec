<?php

namespace App\View;

use Illuminate\Auth\AuthManager;
use Illuminate\View\View;

class LoginCreator
{
    protected $user;

    public function __construct(AuthManager $auth)
    {
        $this->user = $auth->user();
    }

    /**
     * データをビューと結合
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function create(View $view)
    {
        $view->with([
            'user' => $this->user,
        ]);
    }
}
