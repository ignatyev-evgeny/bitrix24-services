<?php

use App\Http\Controllers\Services\BitrixController;
use App\Http\Middleware\AuthUserByMemberID;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('services')->name('services.')->group(function () {
    Route::prefix('bitrix')->name('bitrix.')->group(function () {
        Route::prefix('certification')->name('certification.')->group(function () {
            Route::post('/install', [BitrixController::class, 'install'])->withoutMiddleware([VerifyCsrfToken::class, AuthUserByMemberID::class])->name('install');
            Route::get('/home/{member_id}', [BitrixController::class, 'home'])->middleware([AuthUserByMemberID::class])->name('home');
            Route::get('/users/{member_id}', [BitrixController::class, 'getUsers'])->middleware([AuthUserByMemberID::class])->name('get.users');
            Route::get('/departments/{member_id}', [BitrixController::class, 'getDepartments'])->middleware([AuthUserByMemberID::class])->name('get.departments');
            Route::get('/tests/{member_id}', [BitrixController::class, 'getTests'])->middleware([AuthUserByMemberID::class])->name('get.tests');
            Route::get('/certifications/{member_id}', [BitrixController::class, 'getCertifications'])->middleware([AuthUserByMemberID::class])->name('get.сertifications');
        });
    });
});