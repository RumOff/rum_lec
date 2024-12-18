<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CompanyAdminController;
use App\Http\Controllers\Admin\DownloadController;
use App\Http\Controllers\Admin\SurveyController;
use App\Http\Controllers\Admin\SurveyDeliveryController;
use App\Http\Controllers\Admin\SurveyQuestionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:admin')->group(function () {
    // Route::redirect('/', '/login');
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login-form');
    Route::post('login', [AuthController::class, 'login'])->name('login');
});

Route::middleware('auth:admin')->group(function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('superadmin')->group(function () {
        Route::prefix('admins')->as('admins.')->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('index');
            Route::post('/', [AdminController::class, 'store'])->name('store');
            Route::get('create', [AdminController::class, 'create'])->name('create');
        });
    });
    Route::prefix('admins')->as('admins.')->group(function () {
        Route::patch('{adminId}', [AdminController::class, 'update'])->name('update');
        Route::delete('{adminId}', [AdminController::class, 'destroy'])->name('destroy');
        Route::get('{adminId}/edit', [AdminController::class, 'edit'])->name('edit');
    });

    Route::prefix('downloads')->as('downloads.')->group(function () {
        Route::get('/', [DownloadController::class, 'index'])->name('index');
        Route::post('by-company', [DownloadController::class, 'byCompany'])->name('by-company');
        Route::post('by-period', [DownloadController::class, 'byPeriod'])->name('by-period');
        Route::post('by-survey', [DownloadController::class, 'bySurvey'])->name('by-survey');
    });

    Route::prefix('companies')->as('companies.')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->name('index');
        Route::post('/', [CompanyController::class, 'store'])->name('store');
        Route::get('create', [CompanyController::class, 'create'])->name('create');

        Route::prefix('{companyId}')->group(function () {
            Route::patch('/', [CompanyController::class, 'update'])->name('update');
            Route::get('/', [CompanyController::class, 'show'])->name('show');
            Route::delete('/', [CompanyController::class, 'destroy'])->name('destroy');
            Route::get('/edit', [CompanyController::class, 'edit'])->name('edit');

            Route::prefix('admins')->as('admins.')->group(function () {
                Route::get('/assign', [CompanyAdminController::class, 'assign'])->name('assign'); // 担当者割り当てページ
                Route::post('/assign', [CompanyAdminController::class, 'store'])->name('store'); // 担当者割り当て処理
                Route::delete('{adminId}', [CompanyAdminController::class, 'destroy'])->name('destroy'); // 担当者解除
            });

            Route::prefix('surveys')->as('surveys.')->group(function () {
                Route::post('/', [SurveyController::class, 'store'])->name('store');
                Route::post('confirm', [SurveyController::class, 'confirm'])->name('confirm');
                Route::get('create', [SurveyController::class, 'create'])->name('create');

                Route::prefix('{surveyId}')->group(function () {
                    Route::patch('/', [SurveyController::class, 'update'])->name('update');
                    Route::get('/', [SurveyController::class, 'show'])->name('show');
                    Route::delete('/', [SurveyController::class, 'destroy'])->name('destroy');
                    Route::get('edit', [SurveyController::class, 'edit'])->name('edit');
                    Route::get('results-all', [SurveyController::class, 'resultsAll'])->name('results-all');
                    Route::get('results/{surveyTargetUserId}', [SurveyController::class, 'results'])->name('results');
                    Route::get('results/{surveyTargetUserId}/edit', [SurveyController::class, 'editResults'])->name('results.edit');
                    Route::patch('results/{surveyTargetUserId}', [SurveyController::class, 'updateResults'])->name('results.update');

                    Route::prefix('user')->as('user.')->group(function () {
                        Route::post('/', [SurveyController::class, 'storeUser'])->name('store');
                        Route::get('create', [SurveyController::class, 'createUser'])->name('create');
                        Route::prefix('{surveyTargetUserId}')->group(function () {
                            Route::patch('/', [SurveyController::class, 'updateUser'])->name('update');
                            Route::get('/edit', [SurveyController::class, 'editUser'])->name('edit');
                            Route::delete('/', [SurveyController::class, 'destroyUser'])->name('destroy');
                        });
                    });

                    Route::prefix('survey-questions')->as('survey-questions.')->group(function () {
                        Route::patch('/', [SurveyQuestionController::class, 'update'])->name('update');
                        Route::get('edit', [SurveyQuestionController::class, 'edit'])->name('edit');
                    });

                    Route::prefix('survey-deliveries')->as('survey-deliveries.')->group(function () {
                        Route::post('/', [SurveyDeliveryController::class, 'store'])->name('store');
                        Route::get('create', [SurveyDeliveryController::class, 'create'])->name('create');
                        Route::prefix('{surveyDeliveryId}')->group(function () {
                            Route::get('/', [SurveyDeliveryController::class, 'show'])->name('show');
                            Route::delete('/', [SurveyDeliveryController::class, 'destroy'])->name('destroy');
                        });
                    });
                });
            });
        });
    });
    Route::get('get-default-template', [SurveyDeliveryController::class, 'getDefaultTemplate'])
        ->name('survey-deliveries.get_default_template');
    Route::get('get-remind-template', [SurveyDeliveryController::class, 'getRemindTemplate'])
        ->name('survey-deliveries.get_remind_template');
});

Route::post('/creative-survey', [SurveyController::class, 'complete']);
