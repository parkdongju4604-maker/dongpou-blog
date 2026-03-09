<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Admin\StatsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// ── 공개 블로그 라우트
Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/category/{category}', [PostController::class, 'category'])->name('posts.category');
Route::get('/posts/{slug}', [PostController::class, 'show'])->name('posts.show');

// ── SEO
Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// ── 관리자 로그인/로그아웃 (인증 불필요)
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// ── 관리자 (인증 필요)
Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {

    // 대시보드
    Route::get('/', [PostController::class, 'dashboard'])->name('dashboard');

    // 글 관리
    Route::get('/posts', [PostController::class, 'adminIndex'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // 카테고리 관리
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // 사이트 통계
    Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');

    // 사이트 설정
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // 이미지 업로드 (그룹 prefix 'admin.' 이 자동 붙으므로 여기선 upload.image 만)
    Route::post('/upload/image', [ImageUploadController::class, 'store'])->name('upload.image');
    Route::delete('/upload/image', [ImageUploadController::class, 'destroy'])->name('upload.image.destroy');
});
