<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EWalletController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/deposit', [PaymentController::class, 'deposit']);
    Route::post('/withdraw', [PaymentController::class, 'withdraw']);
    Route::get('/ewallet/balance', [EWalletController::class, 'getBalance']);
    Route::get('/transactions/history', [PaymentController::class, 'transactionHistory']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
