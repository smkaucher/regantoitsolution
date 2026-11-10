<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [\App\Http\Controllers\SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [\App\Http\Controllers\SeoController::class, 'robots'])->name('robots');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/services/{slug}', [PageController::class, 'service'])->name('services.show');
Route::get('/projects', [PageController::class, 'projects'])->name('projects');
Route::get('/projects/{slug}', [PageController::class, 'project'])->name('projects.show');
Route::get('/team', [PageController::class, 'team'])->name('team');
Route::get('/blog', [PageController::class, 'blogs'])->name('blog');
Route::get('/blog/{slug}', [PageController::class, 'blog'])->name('blog.show');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/ai-chat', [\App\Http\Controllers\AiChatController::class, 'send'])->middleware('throttle:20,1')->name('ai.chat');

// ---------- Admin ----------
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [\App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.submit');

    Route::middleware('admin')->group(function () {
        Route::post('logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'edit'])->name('settings');
        Route::patch('services/{service}/toggle', [\App\Http\Controllers\Admin\ServiceController::class, 'toggle'])->name('services.toggle');
        Route::resource('services', \App\Http\Controllers\Admin\ServiceController::class)->except('show');
        Route::patch('projects/{project}/toggle', [\App\Http\Controllers\Admin\ProjectController::class, 'toggle'])->name('projects.toggle');
        Route::resource('projects', \App\Http\Controllers\Admin\ProjectController::class)->except('show');
        Route::prefix('content/{type}')->where(['type' => 'testimonials|faqs|stats|clients|blogs', 'id' => '[0-9]+'])->name('content.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ContentController::class, 'index'])->name('index');
            Route::get('create', [\App\Http\Controllers\Admin\ContentController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\ContentController::class, 'store'])->name('store');
            Route::get('{id}/edit', [\App\Http\Controllers\Admin\ContentController::class, 'edit'])->name('edit');
            Route::put('{id}', [\App\Http\Controllers\Admin\ContentController::class, 'update'])->name('update');
            Route::patch('{id}/toggle', [\App\Http\Controllers\Admin\ContentController::class, 'toggle'])->name('toggle');
            Route::delete('{id}', [\App\Http\Controllers\Admin\ContentController::class, 'destroy'])->name('destroy');
        });
        Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    });
});
