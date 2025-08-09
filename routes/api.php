<?php

use Illuminate\Support\Facades\Route;
use Payra\Http\Controllers\PayraSignatureController;

Route::post('/payra/sign', PayraSignatureController::class);
