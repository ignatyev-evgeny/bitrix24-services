<?php

use App\Http\Controllers\Services\BitrixController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('services')->name('services.')->group(function () {
    Route::prefix('bitrix')->name('bitrix.')->group(function () {
        Route::prefix('quiz')->name('quiz.')->group(function () {
            Route::get('/home', [BitrixController::class, 'home'])->name('home');
            Route::post('/install', [BitrixController::class, 'install'])->withoutMiddleware([VerifyCsrfToken::class])->name('install');
        });
    });
});