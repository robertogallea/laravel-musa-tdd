<?php

use App\Http\Middleware\OnlyInTheMorning;
use App\Http\Requests\ConversionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/convert', \App\Http\Controllers\Api\ConversionController::class)->middleware(OnlyInTheMorning::class);
