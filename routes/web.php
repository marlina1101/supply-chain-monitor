<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\EconomyController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\RiskController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GlobalCountryController;
use App\Http\Controllers\ProfileController;

// ===== ROOT =====
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// ===== HALAMAN USER (harus login) =====
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard',     [DashboardController::class,     'index'])->name('dashboard');
    Route::get('/weather',       [WeatherController::class,       'index'])->name('weather');
    Route::get('/economy',       [EconomyController::class,       'index'])->name('economy');
    Route::get('/country',       [CountryController::class,       'index'])->name('country');
    Route::get('/currency',      [CurrencyController::class,      'index'])->name('currency');
    Route::get('/port',          [PortController::class,          'index'])->name('port');
    Route::get('/news',          [NewsController::class,          'index'])->name('news');
    Route::get('/globalcountry', [GlobalCountryController::class, 'index'])->name('globalcountry');
    Route::get('/risk',          [RiskController::class,          'index'])->name('risk');
    Route::get('/compare',       [CompareController::class,       'index'])->name('compare');

    // Watchlist
    Route::get('/watchlist',         [WatchlistController::class, 'index'])->name('watchlist');
    Route::post('/watchlist/add',    [WatchlistController::class, 'add'])->name('watchlist.add');
    Route::delete('/watchlist/{id}', [WatchlistController::class, 'remove'])->name('watchlist.remove');

    // Profile
    Route::get('/profile',   [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ===== ADMIN PANEL (harus login + role admin) =====
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',                    [AdminController::class, 'index'])->name('dashboard');
    Route::get('/users',               [AdminController::class, 'users'])->name('users');
    Route::post('/users/store',        [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{id}',          [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}',       [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::patch('/users/{id}/toggle', [AdminController::class, 'toggleUser'])->name('users.toggle');

    Route::get('/articles',            [AdminController::class, 'articles'])->name('articles');
    Route::post('/articles/store',     [AdminController::class, 'storeArticle'])->name('articles.store');
    Route::put('/articles/{id}',       [AdminController::class, 'updateArticle'])->name('articles.update');
    Route::delete('/articles/{id}',    [AdminController::class, 'deleteArticle'])->name('articles.delete');

    Route::get('/ports',               [AdminController::class, 'ports'])->name('ports');
    Route::post('/ports/store',        [AdminController::class, 'storePort'])->name('ports.store');
    Route::put('/ports/{id}',          [AdminController::class, 'updatePort'])->name('ports.update');
    Route::delete('/ports/{id}',       [AdminController::class, 'deletePort'])->name('ports.delete');

    Route::get('/api-monitor',         [AdminController::class, 'apiMonitor'])->name('api.monitor');
    Route::get('/audit-log',           [AdminController::class, 'auditLog'])->name('audit.log');
    Route::get('/settings',            [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings/update',    [AdminController::class, 'updateSettings'])->name('settings.update');
});

// ===== REST API (public) =====
Route::prefix('api')->group(function () {
    Route::get('/countries', [CountryController::class,  'api']);
    Route::get('/risk',      [RiskController::class,     'api']);
    Route::get('/ports',     [PortController::class,     'api']);
    Route::get('/news',      [NewsController::class,     'api']);
    Route::get('/currency',  [CurrencyController::class, 'api']);
});

// ===== AUTH ROUTES (login, register, dll) =====
require __DIR__.'/auth.php';