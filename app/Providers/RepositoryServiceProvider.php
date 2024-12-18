<?php

namespace App\Providers;

use App\Repositories\Admin\AdminRepository;
use App\Repositories\Admin\AdminRepositoryEloquent;
use App\Repositories\Company\CompanyRepository;
use App\Repositories\Company\CompanyRepositoryEloquent;
use App\Repositories\Survey\SurveyRepository;
use App\Repositories\Survey\SurveyRepositoryEloquent;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryEloquent;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AdminRepository::class, AdminRepositoryEloquent::class);
        $this->app->singleton(CompanyRepository::class, CompanyRepositoryEloquent::class);
        $this->app->singleton(SurveyRepository::class, SurveyRepositoryEloquent::class);
        $this->app->singleton(UserRepository::class, UserRepositoryEloquent::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
