<?php

use App\Http\Controllers\PageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/main-page/imfeelinglucky',[PageController::class, 'imfeelingLucky']);
Route::post('/main-page/history', [PageController::class, 'history']);
Route::post('/main-page/deactivate', [PageController::class, 'deactivateLink']);
Route::post('/main-page/generate', [PageController::class, 'generateLink']);
