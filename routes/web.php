<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\EconomyController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\NewsController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/weather', [WeatherController::class, 'index'])->name('weather');
Route::get('/economy', [EconomyController::class, 'index'])->name('economy');
Route::get('/country', [CountryController::class, 'index'])->name('country');
Route::get('/currency', [CurrencyController::class, 'index'])->name('currency');
Route::get('/port', [PortController::class, 'index'])->name('port');
Route::get('/news', [NewsController::class, 'index'])->name('news');