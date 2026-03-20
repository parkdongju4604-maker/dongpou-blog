<?php

use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\ImageApiController;
use App\Http\Controllers\Api\PostApiController;
use App\Http\Controllers\Api\SettingApiController;
use Illuminate\Support\Facades\Route;

/*
 * API 라우트 (prefix: /api, middleware: api.token)
 * 인증: Authorization: Bearer {token}
 */
Route::middleware('api.token')->group(function () {
    Route::get('/settings',                [SettingApiController::class,  'index']);
    Route::patch('/settings',              [SettingApiController::class,  'update']);
    Route::get('/categories',              [CategoryApiController::class, 'index']);
    Route::post('/categories',             [CategoryApiController::class, 'store']);
    Route::delete('/categories/{category}',[CategoryApiController::class, 'destroy']);
    Route::get('/posts',                   [PostApiController::class,     'index']);
    Route::post('/posts',                  [PostApiController::class,     'store']);
    Route::post('/images',                 [ImageApiController::class,    'store']);
});
