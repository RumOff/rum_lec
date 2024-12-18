<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return redirect('/admin/login');
});

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::get('health-check', function () {
    return view('health-check');
});
