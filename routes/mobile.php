<?php

use App\Http\Controllers\Mobile\MobileAuthController;
use App\Http\Controllers\Mobile\MobileProfileController;
use App\Http\Controllers\Mobile\MobileSensorController;
use App\Http\Middleware\DevAuthHeaderLogger;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile API (JSON only, no /api prefix)
|--------------------------------------------------------------------------
*/
$baseMiddleware = [];
if (app()->environment('local')) {
    $baseMiddleware[] = DevAuthHeaderLogger::class;
}

Route::middleware($baseMiddleware)->group(function () {
    Route::post('/M_register', [MobileAuthController::class, 'register']);
    Route::post('/M_login', [MobileAuthController::class, 'login']);

    Route::middleware('mobile.auth')->group(function () {
        Route::post('/M_logout', [MobileAuthController::class, 'logout']);
        Route::get('/M_me', [MobileAuthController::class, 'me']);
        Route::post('/M_update_profile', [MobileProfileController::class, 'updateProfile']);
        Route::post('/M_change_password', [MobileProfileController::class, 'changePassword']);
        Route::post('/M_sensor_ingest', [MobileSensorController::class, 'ingest']);
    });
});
