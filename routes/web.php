<?php

use App\Http\Controllers\Admin\AwardHistoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LuckyWheelController as AdminLuckyWheelController;
use App\Http\Controllers\Admin\PrizeController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Client\LuckyWheelController as ClientLuckyWheelController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Client Routes
Route::get('/', [ClientLuckyWheelController::class, 'index'])->name('home');
Route::post('/register', [ClientLuckyWheelController::class, 'register'])->name('register');
Route::post('/spin', [ClientLuckyWheelController::class, 'spin'])->name('spin');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Lucky Wheel Routes
    Route::resource('lucky-wheels', AdminLuckyWheelController::class);

    // Prize Routes
    Route::resource('lucky-wheels.prizes', PrizeController::class);

    // Award History Routes
    Route::get('award-histories', [AwardHistoryController::class, 'index'])->name('award-histories.index');
    Route::get('award-histories/export', [AwardHistoryController::class, 'export'])->name('award-histories.export');
    Route::get('award-histories/{awardHistory}', [AwardHistoryController::class, 'show'])->name('award-histories.show');
});

// API Routes for Locations
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/provinces', [LocationController::class, 'provinces'])->name('provinces');
    Route::get('/provinces/{province}/districts', [LocationController::class, 'districts'])->name('districts');
    Route::get('/districts/{district}/wards', [LocationController::class, 'wards'])->name('wards');
});
