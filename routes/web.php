<?php

use App\Http\Controllers\DepartmentsController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\QuestionsController;
use App\Http\Controllers\Services\BitrixController;
use App\Http\Controllers\TestsController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\UserIsActiveMiddleware;
use App\Http\Middleware\UserIsAdminMiddleware;
use App\Http\Middleware\UserIsAdminOrSupportMiddleware;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('services')->name('services.')->group(function () {
    Route::prefix('bitrix')->name('bitrix.')->group(function () {
        Route::prefix('certification')->name('certification.')->group(function () {
            Route::post('/install', [BitrixController::class, 'install'])->withoutMiddleware([VerifyCsrfToken::class])->name('install');
        });
    });
});

Route::get('/home/{member_id}', [MainController::class, 'home'])->middleware([UserIsActiveMiddleware::class])->name('home');
Route::get('/users/{member_id}', [UserController::class, 'getUsers'])->middleware([UserIsActiveMiddleware::class])->name('get.users');
Route::get('/departments/{member_id}', [DepartmentsController::class, 'getDepartments'])->middleware([UserIsActiveMiddleware::class])->name('get.departments');
Route::get('/tests/{member_id}', [MainController::class, 'getTests'])->middleware([UserIsActiveMiddleware::class])->name('get.tests');
Route::get('/certifications/{member_id}', [MainController::class, 'getCertifications'])->middleware([UserIsActiveMiddleware::class])->name('get.сertifications');

Route::prefix('user')->name('user.')->group(function () {
    Route::post('/updateActive/{member_id}', [UserController::class, 'updateActive'])->middleware([UserIsActiveMiddleware::class, UserIsAdminOrSupportMiddleware::class])->withoutMiddleware([VerifyCsrfToken::class])->name('updateActive');
    Route::post('/updateIsSupport/{member_id}', [UserController::class, 'updateIsSupport'])->middleware([UserIsActiveMiddleware::class, UserIsAdminMiddleware::class])->withoutMiddleware([VerifyCsrfToken::class])->name('updateIsSupport');
});

Route::prefix('departments')->name('departments.')->group(function () {
    Route::post('/setManagers/{member_id}', [DepartmentsController::class, 'setManagers'])->middleware([UserIsActiveMiddleware::class, UserIsAdminOrSupportMiddleware::class])->withoutMiddleware([VerifyCsrfToken::class])->name('setManagers');
});

Route::prefix('questions')->name('questions.')->group(function () {
    Route::get('/list/{member_id}', [QuestionsController::class, 'list'])->middleware([UserIsActiveMiddleware::class, UserIsAdminOrSupportMiddleware::class])->name('list');
    Route::get('/create/{member_id}', [QuestionsController::class, 'create'])->middleware([UserIsActiveMiddleware::class, UserIsAdminOrSupportMiddleware::class])->name('create');
    Route::post('/store/{member_id}', [QuestionsController::class, 'store'])->middleware([UserIsActiveMiddleware::class, UserIsAdminOrSupportMiddleware::class])->name('store');
    Route::get('/show/{question}/{member_id}', [QuestionsController::class, 'show'])->middleware([UserIsActiveMiddleware::class, UserIsAdminOrSupportMiddleware::class])->name('show');
    Route::post('/update/{question}/{member_id}', [QuestionsController::class, 'update'])->middleware([UserIsActiveMiddleware::class, UserIsAdminOrSupportMiddleware::class])->name('update');
});

Route::prefix('tests')->name('tests.')->group(function () {
    Route::get('/list/{member_id}', [TestsController::class, 'list'])->middleware([UserIsActiveMiddleware::class, UserIsAdminOrSupportMiddleware::class])->name('list');
    Route::get('/create/{member_id}', [TestsController::class, 'create'])->middleware([UserIsActiveMiddleware::class, UserIsAdminOrSupportMiddleware::class])->name('create');
    Route::post('/store/{member_id}', [TestsController::class, 'store'])->middleware([UserIsActiveMiddleware::class, UserIsAdminOrSupportMiddleware::class])->name('store');
    Route::get('/show/{test}/{member_id}', [TestsController::class, 'show'])->middleware([UserIsActiveMiddleware::class, UserIsAdminOrSupportMiddleware::class])->name('show');
    Route::post('/update/{test}/{member_id}', [TestsController::class, 'update'])->middleware([UserIsActiveMiddleware::class, UserIsAdminOrSupportMiddleware::class])->name('update');
});