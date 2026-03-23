<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Admin\ApiTokenController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Admin\CssThemeController;
use App\Http\Controllers\Admin\StatsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

// ── 공개 블로그 라우트
Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/category/{category}', [PostController::class, 'category'])->name('posts.category.legacy');
Route::get('/posts/{slug}', [PostController::class, 'show'])->name('posts.show.legacy');

// ── 검색
Route::get('/search', [SearchController::class, 'index'])->name('search');

// ── 태그
Route::get('/tags/{slug}',  [TagController::class, 'index'])->name('tags.show');
Route::get('/api/tags/all', [TagController::class, 'all'])->name('tags.all');

// ── 댓글
Route::post('/posts/{post}/comments',      [CommentController::class, 'store'])->name('comments.store');
Route::delete('/comments/{comment}',       [CommentController::class, 'destroy'])->name('comments.destroy');

// ── SEO
Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// ── 피드
Route::get('/feed/rss',  [FeedController::class, 'rss'])->name('feed.rss');
Route::get('/feed/atom', [FeedController::class, 'atom'])->name('feed.atom');
Route::view('/privacy-policy', 'privacy-policy')->name('privacy.policy');

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
    Route::post('/posts/preview', [PostController::class, 'preview'])->name('posts.preview');

    // 카테고리 관리
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::post('/categories/suggestions', [CategoryController::class, 'suggestions'])->name('categories.suggestions');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // 사이트 통계
    Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');

    // CSS 테마 관리
    Route::get('/themes', [CssThemeController::class, 'index'])->name('themes.index');
    Route::post('/themes', [CssThemeController::class, 'store'])->name('themes.store');
    Route::post('/themes/sync', [CssThemeController::class, 'sync'])->name('themes.sync');
    Route::get('/themes/{theme}/edit', [CssThemeController::class, 'edit'])->name('themes.edit');
    Route::put('/themes/{theme}', [CssThemeController::class, 'update'])->name('themes.update');
    Route::patch('/themes/{theme}/activate', [CssThemeController::class, 'activate'])->name('themes.activate');
    Route::delete('/themes/{theme}', [CssThemeController::class, 'destroy'])->name('themes.destroy');

    // 사이트 설정
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // 댓글 관리
    Route::get('/comments',                        [AdminCommentController::class, 'index'])->name('comments.index');
    Route::patch('/comments/{comment}/approve',    [AdminCommentController::class, 'approve'])->name('comments.approve');
    Route::patch('/comments/{comment}/spam',       [AdminCommentController::class, 'spam'])->name('comments.spam');
    Route::delete('/comments/{comment}',           [AdminCommentController::class, 'destroy'])->name('comments.destroy');
    Route::delete('/comments-spam/purge',          [AdminCommentController::class, 'destroySpam'])->name('comments.purge-spam');

    // API 토큰 관리
    Route::get('/api-tokens',              [ApiTokenController::class, 'index'])->name('api-tokens.index');
    Route::post('/api-tokens',             [ApiTokenController::class, 'store'])->name('api-tokens.store');
    Route::delete('/api-tokens/{apiToken}',[ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');

    // API 문서
    Route::get('/api-docs', [ApiTokenController::class, 'docs'])->name('api-docs');

    // 이미지 업로드 (그룹 prefix 'admin.' 이 자동 붙으므로 여기선 upload.image 만)
    Route::post('/upload/image', [ImageUploadController::class, 'store'])->name('upload.image');
    Route::delete('/upload/image', [ImageUploadController::class, 'destroy'])->name('upload.image.destroy');

    // 보안 관리 (차단 규칙 + 접속 로그)
    Route::get('/security',                          [SecurityController::class, 'index'])->name('security.index');
    Route::post('/security/rules',                   [SecurityController::class, 'store'])->name('security.rules.store');
    Route::patch('/security/rules/{rule}/toggle',    [SecurityController::class, 'toggle'])->name('security.rules.toggle');
    Route::delete('/security/rules/{rule}',          [SecurityController::class, 'destroy'])->name('security.rules.destroy');
    Route::delete('/security/logs',                  [SecurityController::class, 'clearLogs'])->name('security.logs.clear');
});

// ── 공개 canonical 라우트 (기존 정적 라우트/관리자 라우트보다 뒤에 배치)
$publicReserved = 'admin|api|feed|posts|category|search|tags|privacy-policy|sitemap\.xml|robots\.txt';
Route::get('/{categorySlug}/{slug}', [PostController::class, 'showByCategory'])
    ->where('categorySlug', "^(?!({$publicReserved})$).+")
    ->name('posts.show');
Route::get('/{categorySlug}', [PostController::class, 'categoryBySlug'])
    ->where('categorySlug', "^(?!({$publicReserved})$).+")
    ->name('posts.category');
