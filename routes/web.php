<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('register');
});

Route::post('/register', [UserController::class, 'register'])->name('register');

Route::get('/main-page', [PageController::class, 'mainPage'])->name('main-page');
