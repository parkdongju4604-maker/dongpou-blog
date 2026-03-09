<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

// 블로그 공개 라우트
Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/category/{category}', [PostController::class, 'category'])->name('posts.category');
Route::get('/posts/{slug}', [PostController::class, 'show'])->name('posts.show');

// 관리자 라우트
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/posts', [PostController::class, 'adminIndex'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
});
