<?php

use App\Http\Controllers\admin\AdminNewsCategoryController;
use App\Http\Controllers\admin\AdminNewsController;
use App\Http\Controllers\admin\AdminPageController;
use App\Http\Controllers\admin\AdminServiceCategoryController;
use App\Http\Controllers\admin\AdminServiceController;
use App\Http\Controllers\admin\AdminUserRequestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsCategoryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRequestController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::apiResource('/news-category', NewsCategoryController::class);
Route::apiResource('/news', NewsController::class);
Route::apiResource('/services-category', ServiceCategoryController::class);
Route::apiResource('/services', ServiceController::class);

Route::middleware('auth:api')->prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::patch('/', [UserController::class, 'update']);
    Route::delete('/', [UserController::class, 'destroy']);
    Route::apiResource('/request', UserRequestController::class);
});

Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminPageController::class, 'getInformation']);

    Route::apiResource('/news-category', AdminNewsCategoryController::class)
        ->names('admin.news-category');

    Route::apiResource('/news', AdminNewsController::class)->names('admin.news');

    Route::apiResource('/services-category', AdminServiceCategoryController::class)
        ->names('admin.service-category');

    Route::apiResource('/services', AdminServiceController::class)->names('admin.service');
    Route::get('/requests', [AdminUserRequestController::class, 'showRequests']);
    Route::patch('/requests/{id}/status', [AdminUserRequestController::class, 'changeStatus']);
});

// добавить изменение пароля
// создать сущность, миграцию, запрос, ответ, применить миграцию, создать репо, сервис, контроллер,

// todo создать акции, фотки, сделать админку, добавить изменение пароля
// todo решить проблему с заявкой и несуществующей услугой
// todo ДОКУМЕНТАЦИЯ таблица компании, отзывы,
// todo  на странице админки кол-во пользователй, отзывов, услуг, новостей
