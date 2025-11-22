<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\SitemapController;

// Language switching route
Route::get('/lang/{locale}', [App\Http\Controllers\LanguageController::class, 'switch'])->name('language.switch');

// Public routes
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Redirect old dashboard route to admin dashboard
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware('auth');
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{id}', [ArticleController::class, 'show'])
    ->middleware('track.views')
    ->name('articles.show');
Route::get('/search', [ArticleController::class, 'index'])->name('search');
Route::get('/api/search/suggestions', [ArticleController::class, 'suggestions'])->name('search.suggestions');

// Newsletter routes
Route::post('/newsletter/subscribe', [App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [App\Http\Controllers\NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// SEO routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-articles.xml', [SitemapController::class, 'articles'])->name('sitemap.articles');

// Admin routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    Route::resource('articles', AdminArticleController::class);
    Route::post('articles/{article}/publish', [AdminArticleController::class, 'publish'])->name('articles.publish');
    Route::post('articles/{article}/unpublish', [AdminArticleController::class, 'unpublish'])->name('articles.unpublish');
    Route::post('articles/bulk-action', [AdminArticleController::class, 'bulkAction'])->name('articles.bulk-action');
    
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
});
