<?php

use Illuminate\Support\Facades\Route;
use Payra\Http\Controllers\SignatureController;

Route::post('/payra/sign', SignatureController::class);
