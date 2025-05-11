<?php

use App\Http\Controllers\admin\AdminNewsCategoryController;
use App\Http\Controllers\admin\AdminNewsController;
use App\Http\Controllers\admin\AdminPageController;
use App\Http\Controllers\admin\AdminServiceController;
use App\Http\Controllers\admin\AdminUserRequestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsCategoryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRequestController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::apiResource('/news-category', NewsCategoryController::class);
Route::apiResource('/news', NewsController::class);
Route::apiResource('/service', ServiceController::class);

Route::middleware('auth:api')->prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::patch('/', [UserController::class, 'update']);
    Route::delete('/', [UserController::class, 'destroy']);
    Route::apiResource('/request', UserRequestController::class);

});

Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminPageController::class, 'getInformation']);
    Route::apiResource('/news-category', AdminNewsCategoryController::class);
    Route::apiResource('/news', AdminNewsController::class);
    Route::apiResource('/service', AdminServiceController::class);
    Route::get('/requests', [AdminUserRequestController::class, 'showRequests']);
    Route::patch('/requests/{id}/status', [AdminUserRequestController::class, 'changeStatus']);
});


// добавить изменение пароля, добаить swagger
// создать сущность, создать миграцию, применить миграцию, создать репо, сервис, контроллер, создать дто
// доделать новости, после обсуждения

// todo создать акции, фотки, сделать админку.
// todo решить проблему с заявкой и несуществующей услугой
// todo категории для новостей и для услуг, таблица компании, отзывы,
// todo  на странице админки кол-во пользователй, отзывов, услуг, новостей
