<?php

use Illuminate\Support\Facades\Route;
use Payra\Http\Controllers\PayraSignatureController;
use Payra\Http\Controllers\PayraOrderServiceController;
use Payra\Http\Controllers\PayraUtilsController;

Route::prefix('api')->middleware('payra.auth')->group(function () {
    Route::post('payra/generate/signature', PayraSignatureController::class);
    Route::post('payra/order/details', [PayraOrderServiceController::class, 'getDetails']);
    Route::post('payra/order/is-paid', [PayraOrderServiceController::class, 'isPaid']);
    Route::post('payra/convert/to/usd', [PayraUtilsController::class, 'convertToUSD']);
});