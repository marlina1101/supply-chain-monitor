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


Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Fitur baru
Route::get('/risk',      [RiskController::class, 'index'])->name('risk');
Route::get('/compare',   [CompareController::class, 'index'])->name('compare');
Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist');
Route::post('/watchlist/add',    [WatchlistController::class, 'add'])->name('watchlist.add');
Route::delete('/watchlist/{id}', [WatchlistController::class, 'remove'])->name('watchlist.remove');
Route::get('/admin', [AdminController::class, 'index'])->name('admin');

// REST API
Route::prefix('api')->group(function () {
    Route::get('/risk',     [RiskController::class, 'api']);
    Route::get('/countries',[CountryController::class, 'api']);
    Route::get('/ports',    [PortController::class, 'api']);
    Route::get('/news',     [NewsController::class, 'api']);
    Route::get('/currency', [CurrencyController::class, 'api']);
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/weather', [WeatherController::class, 'index'])->name('weather');
Route::get('/economy', [EconomyController::class, 'index'])->name('economy');
Route::get('/country', [CountryController::class, 'index'])->name('country');
Route::get('/currency', [CurrencyController::class, 'index'])->name('currency');
Route::get('/port', [PortController::class, 'index'])->name('port');
Route::get('/news', [NewsController::class, 'index'])->name('news');