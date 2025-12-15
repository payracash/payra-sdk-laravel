<?php

use Illuminate\Support\Facades\Route;
use Payra\Http\Controllers\PayraSignatureController;
use Payra\Http\Controllers\PayraVerificationController;
use Payra\Http\Controllers\PayraUtilsController;

Route::prefix('api')->middleware('payra.auth')->group(function () {
    Route::post('/payra/generate/signature', PayraSignatureController::class);
    Route::post('/payra/order/verification', PayraVerificationController::class);
    Route::post('/payra/convert/to/usd', [PayraUtilsController::class, 'convertToUSD']);
});
