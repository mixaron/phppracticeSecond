<?php

use App\Http\Controllers\admin\AdminNewsCategoryController;
use App\Http\Controllers\admin\AdminNewsController;
use App\Http\Controllers\admin\AdminPageController;
use App\Http\Controllers\admin\AdminReviewController;
use App\Http\Controllers\admin\AdminServiceCategoryController;
use App\Http\Controllers\admin\AdminServiceController;
use App\Http\Controllers\admin\AdminUserRequestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsCategoryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\UserReviewController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRequestController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::apiResource('/news-categories', NewsCategoryController::class);
Route::apiResource('/news', NewsController::class);
Route::apiResource('/services-categories', ServiceCategoryController::class);
Route::apiResource('/services', ServiceController::class);

Route::middleware('auth:api')->prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::patch('/', [UserController::class, 'update']);
    Route::delete('/', [UserController::class, 'destroy']);
    Route::patch('/password', [UserController::class, 'changePassword']);
    Route::apiResource('/requests', UserRequestController::class);
    Route::apiResource('/reviews', UserReviewController::class);
});

Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminPageController::class, 'getInformation']);

    Route::get('/users', [AdminPageController::class, 'getAllUsers']);
    Route::delete('/users/{id}', [AdminPageController::class, 'deleteUser']);
    Route::patch('/users/{id}/role', [AdminPageController::class, 'changeUserRole']);


    Route::apiResource('/news-categories', AdminNewsCategoryController::class)
        ->names('admin.news-categories');
    Route::apiResource('/news', AdminNewsController::class)->names('admin.news');

    Route::apiResource('/service-categories', AdminServiceCategoryController::class)
        ->names('admin.service-categories');
    Route::apiResource('/services', AdminServiceController::class)->names('admin.service');

    Route::get('/requests', [AdminUserRequestController::class, 'showRequests']);
    Route::patch('/requests/{id}/status', [AdminUserRequestController::class, 'changeStatus']);

    Route::get('/reviews', [AdminReviewController::class, 'index']);
    Route::patch('/reviews/{id}/status', [AdminReviewController::class, 'changeReviewStatus']);
});

// добавить изменение пароля
// создать сущность, миграцию, запрос, ответ, применить миграцию, создать репо, сервис, контроллер,

// todo создать акции, фотки, сделать админку, добавить изменение пароля
// todo решить проблему с заявкой и несуществующей услугой
// todo ДОКУМЕНТАЦИЯ таблица компании, отзывы,
// доделать админку для отзывов, проверить
