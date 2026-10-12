<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\CaseApiController;
use Illuminate\Support\Facades\Route;

// Public Authentication Endpoints
Route::post('/login', [ApiAuthController::class, 'login'])->name('api.login');

// Protected Endpoints (Requires Sanctum Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout'])->name('api.logout');

    Route::get('/cases', [CaseApiController::class, 'index'])->name('api.cases.index');
    Route::post('/cases', [CaseApiController::class, 'store'])->name('api.cases.store');
    Route::get('/cases/{id}', [CaseApiController::class, 'show'])->name('api.cases.show');
    Route::put('/cases/{id}', [CaseApiController::class, 'update'])->name('api.cases.update');
    Route::delete('/cases/{id}', [CaseApiController::class, 'destroy'])->name('api.cases.destroy');
});
