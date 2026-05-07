<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\AdminAuthController;

Route::get('/', [AdminAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AdminAuthController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

Route::middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Frame Module
    Route::post('frame-categories/update-order', [\App\Http\Controllers\FrameCategoryController::class, 'updateOrder'])->name('frame-categories.update-order');
    Route::post('frame-categories/update-status', [\App\Http\Controllers\FrameCategoryController::class, 'updateStatus'])->name('frame-categories.update-status');
    Route::resource('frame-categories', \App\Http\Controllers\FrameCategoryController::class);
    Route::resource('frames', \App\Http\Controllers\FrameController::class);
    Route::post('frames/update-order', [\App\Http\Controllers\FrameController::class, 'updateOrder'])->name('frames.update-order');


    // Sticker Module
    Route::post('sticker-categories/update-active-status', [\App\Http\Controllers\StickerCategoryController::class, 'updateActiveStatus'])->name('sticker-categories.update-active-status');
    Route::post('sticker-categories/update-status', [\App\Http\Controllers\StickerCategoryController::class, 'updateStatus'])->name('sticker-categories.update-status');
    Route::get('sticker-categories/order', [\App\Http\Controllers\StickerCategoryController::class, 'order'])->name('sticker-categories.order');
    Route::post('sticker-categories/update-order', [\App\Http\Controllers\StickerCategoryController::class, 'updateOrder'])->name('sticker-categories.update-order');
    Route::resource('sticker-categories', \App\Http\Controllers\StickerCategoryController::class);
    Route::resource('stickers', \App\Http\Controllers\StickerController::class);

    // Font Module
    Route::post('fonts/change-type', [\App\Http\Controllers\FontController::class, 'changeType'])->name('fonts.change-type');
    Route::resource('fonts', \App\Http\Controllers\FontController::class);

    // Doodle Module
    Route::post('doodles/change-type', [\App\Http\Controllers\DoodleController::class, 'changeType'])->name('doodles.change-type');
    Route::post('doodles/update-order', [\App\Http\Controllers\DoodleController::class, 'updateOrder'])->name('doodles.update-order');
    Route::resource('doodles', \App\Http\Controllers\DoodleController::class);

    // Filter Category Module
    Route::post('filter-categories/update-status', [\App\Http\Controllers\FilterCategoryController::class, 'updateStatus'])->name('filter-categories.update-status');
    Route::resource('filter-categories', \App\Http\Controllers\FilterCategoryController::class);

    // Filter Module
    Route::get('filters/import', [\App\Http\Controllers\FilterController::class, 'import'])->name('filters.import');
    Route::post('filters/import', [\App\Http\Controllers\FilterController::class, 'importProcess'])->name('filters.import.process');
    Route::post('filters/change-type', [\App\Http\Controllers\FilterController::class, 'changeType'])->name('filters.change-type');
    Route::resource('filters', \App\Http\Controllers\FilterController::class);

    // Background Module
    Route::post('background-categories/update-active-status', [\App\Http\Controllers\BackgroundCategoryController::class, 'updateActiveStatus'])->name('background-categories.update-active-status');
    Route::post('background-categories/update-status', [\App\Http\Controllers\BackgroundCategoryController::class, 'updateStatus'])->name('background-categories.update-status');
    Route::get('background-categories/order', [\App\Http\Controllers\BackgroundCategoryController::class, 'order'])->name('background-categories.order');
    Route::post('background-categories/update-order', [\App\Http\Controllers\BackgroundCategoryController::class, 'updateOrder'])->name('background-categories.update-order');
    Route::resource('background-categories', \App\Http\Controllers\BackgroundCategoryController::class);
    Route::resource('backgrounds', \App\Http\Controllers\BackgroundController::class);

    // System Management
    Route::post('clear-cache-logs', [\App\Http\Controllers\SystemController::class, 'clearCacheAndLogs'])->name('system.clear-cache-logs');

    // API List
    Route::get('/api-list', [\App\Http\Controllers\ApiListController::class, 'index'])->name('api-list');
});
