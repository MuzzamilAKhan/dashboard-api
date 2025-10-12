<?php

use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('user', function (Request $request) {
    $users = User::all();
    Log::info($users);
    response()->json(['data' => $users]);
});
Route::get('ping', function () {
    return response()->json(['message' => 'API working fine!']);
});
Route::get('/reports', [ReportController::class, 'index']);
Route::post('/reports', [ReportController::class, 'store']);
Route::get('/reports/{id}', [ReportController::class, 'show']);
Route::put('/reports/{id}', [ReportController::class, 'update']);
Route::delete('/reports/{id}', [ReportController::class, 'destroy']);

Route::get('/settings', [SettingController::class, 'index']);
