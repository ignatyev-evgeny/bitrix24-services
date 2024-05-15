<?php

use App\Http\Controllers\DepartmentsController;
use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\oAuthController;
use App\Http\Controllers\QuestionsController;
use App\Http\Controllers\Services\BitrixController;
use App\Http\Controllers\TestsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsersGroupsController;
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
            Route::any('/install', [BitrixController::class, 'install'])->withoutMiddleware([UserIsActiveMiddleware::class])->name('install');
        });
    });
});

Route::get('/login', [MainController::class, 'login'])->withoutMiddleware([UserIsActiveMiddleware::class])->name('login');
Route::prefix('oAuth')->name('oAuth.')->group(function () {
    Route::prefix('bitrix')->name('bitrix.')->group(function () {
        Route::post('/login', [oAuthController::class, 'bitrix'])->withoutMiddleware([UserIsActiveMiddleware::class])->name('login');
    });
});

Route::get('/home/{auth_id}', [MainController::class, 'home'])->middleware([UserIsActiveMiddleware::class])->name('home');
Route::get('/users/{auth_id}', [UserController::class, 'getUsers'])->middleware([UserIsActiveMiddleware::class])->name('get.users');
Route::get('/departments/{auth_id}', [DepartmentsController::class, 'getDepartments'])->middleware([UserIsActiveMiddleware::class])->name('get.departments');
Route::get('/tests/{auth_id}', [MainController::class, 'getTests'])->middleware([UserIsActiveMiddleware::class])->name('get.tests');
Route::get('/certifications/{auth_id}', [MainController::class, 'getCertifications'])->middleware([UserIsActiveMiddleware::class])->name('get.сertifications');

Route::prefix('user')->name('user.')->group(function () {
    Route::post('/updateActive/{auth_id}', [UserController::class, 'updateActive'])->middleware([UserIsAdminOrSupportMiddleware::class])->withoutMiddleware([VerifyCsrfToken::class])->name('updateActive');
    Route::post('/updateIsSupport/{auth_id}', [UserController::class, 'updateIsSupport'])->middleware([UserIsAdminMiddleware::class])->withoutMiddleware([VerifyCsrfToken::class])->name('updateIsSupport');
});

Route::prefix('departments')->name('departments.')->group(function () {
    Route::post('/setManagers/{auth_id}', [DepartmentsController::class, 'setManagers'])->middleware([UserIsAdminOrSupportMiddleware::class])->withoutMiddleware([VerifyCsrfToken::class])->name('setManagers');
});

Route::prefix('users')->name('users.')->group(function () {
    Route::prefix('groups')->name('groups.')->group(function () {
        Route::get('/list/{auth_id}', [UsersGroupsController::class, 'list'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('list');
        Route::get('/create/{auth_id}', [UsersGroupsController::class, 'create'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('create');
        Route::post('/store/{auth_id}', [UsersGroupsController::class, 'store'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('store');
        Route::get('/show/{group}/{auth_id}', [UsersGroupsController::class, 'show'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('show');
        Route::post('/update/{group}/{auth_id}', [UsersGroupsController::class, 'update'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('update');
        Route::post('/destroy/{group}/{auth_id}', [UsersGroupsController::class, 'destroy'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('destroy');
    });
});

Route::prefix('questions')->name('questions.')->group(function () {
    Route::get('/list/{auth_id}', [QuestionsController::class, 'list'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('list');
    Route::get('/create/{auth_id}', [QuestionsController::class, 'create'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('create');
    Route::post('/store/{auth_id}', [QuestionsController::class, 'store'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('store');
    Route::get('/show/{question}/{auth_id}', [QuestionsController::class, 'show'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('show');
    Route::post('/update/{question}/{auth_id}', [QuestionsController::class, 'update'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('update');
    Route::post('/destroy/{question}/{auth_id}', [QuestionsController::class, 'destroy'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('destroy');
});

Route::prefix('knowledge')->name('knowledge.')->group(function () {
    Route::get('/list/{auth_id}', [KnowledgeController::class, 'list'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('list');
    Route::get('/create/{auth_id}', [KnowledgeController::class, 'create'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('create');
    Route::post('/store/{auth_id}', [KnowledgeController::class, 'store'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('store');
    Route::get('/show/{knowledge}/{auth_id}', [KnowledgeController::class, 'show'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('show');
    Route::get('/preview/{knowledge}/{auth_id}', [KnowledgeController::class, 'preview'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('preview');
    Route::post('/update/{knowledge}/{auth_id}', [KnowledgeController::class, 'update'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('update');
    Route::post('/destroy/{knowledge}/{auth_id}', [KnowledgeController::class, 'destroy'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('destroy');
});

Route::prefix('tests')->name('tests.')->group(function () {
    Route::get('/list/{auth_id}', [TestsController::class, 'list'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('list');
    Route::get('/create/{auth_id}', [TestsController::class, 'create'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('create');
    Route::post('/store/{auth_id}', [TestsController::class, 'store'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('store');
    Route::get('/show/{test}/{auth_id}', [TestsController::class, 'show'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('show');
    Route::post('/update/{test}/{auth_id}', [TestsController::class, 'update'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('update');
    Route::get('/destroy/{test}/{auth_id}', [TestsController::class, 'destroy'])->middleware([UserIsAdminOrSupportMiddleware::class])->name('destroy');
});

