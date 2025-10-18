<?php

use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Api\Admin\SettingController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'profile']);
    });
});

Route::get('user', function (Request $request) {
    $users = User::all();
    Log::info($users);
    response()->json(['data' => $users]);
});
Route::get('ping', function () {
    return response()->json(['message' => 'API working fine!']);
});

Route::prefix('admin')->group(function () {
    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
});
Route::get('/reports', [ReportController::class, 'index']);
Route::post('/reports', [ReportController::class, 'store']);
Route::get('/reports/{id}', [ReportController::class, 'show']);
Route::put('/reports/{id}', [ReportController::class, 'update']);
Route::delete('/reports/{id}', [ReportController::class, 'destroy']);

Route::get('/settings', [SettingController::class, 'index']);
