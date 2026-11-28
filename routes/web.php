<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home2', [HomeController::class, 'home2'])->name('home2');
Route::get('/_/home/{part}', [HomeController::class, 'section'])->whereIn('part', ['services', 'work', 'features', 'reviews', 'faq'])->name('home.section');
Route::get('/_/services/{slug}', [PageController::class, 'serviceCards'])->name('services.cards');
Route::get('/lang/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['en', 'bn'], true), 404);
    // Back to the page the visitor was on, but only if it is on this site.
    $back = url()->previous();
    $to = str_starts_with($back, url('/')) ? $back : url('/');

    return redirect($to)->withCookie(cookie('lang', $locale, 60 * 24 * 365, '/', null, false, false, false, 'lax'));
})->name('lang');
Route::get('/sitemap.xml', [\App\Http\Controllers\SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [\App\Http\Controllers\SeoController::class, 'robots'])->name('robots');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/services/{slug}', [PageController::class, 'service'])->name('services.show');
Route::get('/services/{slug}/{item}', [PageController::class, 'serviceItem'])->name('services.item');
Route::get('/projects', [PageController::class, 'projects'])->name('projects');
Route::get('/projects/{slug}', [PageController::class, 'project'])->name('projects.show');
Route::get('/team', [PageController::class, 'team'])->name('team');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'store'])->middleware('throttle:6,1')->name('contact.store');
Route::get('/reviews', fn () => redirect(url('/') . '#reviews')); // someone opening the form's address
Route::post('/reviews', [\App\Http\Controllers\TestimonialController::class, 'store'])->middleware('throttle:5,10')->name('reviews.store');
Route::post('/ai-chat', [\App\Http\Controllers\AiChatController::class, 'send'])->middleware('throttle:40,1')->name('ai.chat');

// ---------- Admin ----------
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [\App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.submit');

    // auth.session: a password change signs out every other browser and device.
    Route::middleware(['admin', 'auth.session'])->group(function () {
        Route::post('logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('ping', fn () => response()->noContent())->name('ping'); // keeps the session alive while a page is open
        Route::get('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile');
        Route::put('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->middleware('throttle:10,1')->name('profile.update');
        Route::put('profile/password', [\App\Http\Controllers\Admin\ProfileController::class, 'password'])->middleware('throttle:10,1')->name('profile.password');
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'edit'])->name('settings');
        Route::resource('requests', \App\Http\Controllers\Admin\ProjectRequestController::class)->only(['index', 'show', 'update', 'destroy']);
        Route::patch('services/{service}/toggle', [\App\Http\Controllers\Admin\ServiceController::class, 'toggle'])->name('services.toggle');
        Route::resource('services', \App\Http\Controllers\Admin\ServiceController::class)->except('show');
        Route::prefix('team')->name('team.')->controller(\App\Http\Controllers\Admin\TeamController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('sections/create', 'createGroup')->name('groups.create');
            Route::post('sections', 'storeGroup')->name('groups.store');
            Route::get('sections/{group}/edit', 'editGroup')->name('groups.edit');
            Route::put('sections/{group}', 'updateGroup')->name('groups.update');
            Route::patch('sections/{group}/toggle', 'toggleGroup')->name('groups.toggle');
            Route::delete('sections/{group}', 'destroyGroup')->name('groups.destroy');
            Route::get('people/create', 'createMember')->name('members.create');
            Route::post('people', 'storeMember')->name('members.store');
            Route::get('people/{member}/edit', 'editMember')->name('members.edit');
            Route::put('people/{member}', 'updateMember')->name('members.update');
            Route::patch('people/{member}/toggle', 'toggleMember')->name('members.toggle');
            Route::delete('people/{member}', 'destroyMember')->name('members.destroy');
        });
        Route::patch('projects/{project}/toggle', [\App\Http\Controllers\Admin\ProjectController::class, 'toggle'])->name('projects.toggle');
        Route::resource('projects', \App\Http\Controllers\Admin\ProjectController::class)->except('show');
        Route::prefix('content/{type}')->where(['type' => 'testimonials|faqs|stats|clients', 'id' => '[0-9]+'])->name('content.')->group(function () {
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
