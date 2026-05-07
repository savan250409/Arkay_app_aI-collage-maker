<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('api.auth')->group(function () {
    Route::get('get_frame_category', [\App\Http\Controllers\Api\FrameApiController::class, 'get_frame_category']);
    Route::post('get_frame_by_category_id', [\App\Http\Controllers\Api\FrameApiController::class, 'get_frame_by_category_id']);

    // Collage Assets APIs
    Route::post('get_sticker', [\App\Http\Controllers\Api\CollageApiController::class, 'get_stickers']);
    Route::post('get_fonts', [\App\Http\Controllers\Api\CollageApiController::class, 'get_fonts']);
    Route::post('get_doodle', [\App\Http\Controllers\Api\CollageApiController::class, 'get_doodles']);

    // Background API
    Route::post('get_background', [\App\Http\Controllers\Api\CollageApiController::class, 'get_backgrounds']);

    // Filter API
    Route::post('get_all_filter', [\App\Http\Controllers\Api\FilterApiController::class, 'getAllFilters']);
});
