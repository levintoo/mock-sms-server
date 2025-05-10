<?php

use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::resource('/message', MessageController::class)->only(['index', 'store', 'show', 'destroy']);;

Route::post('/webhook', function (Request $request) {
    Log::info('Webhook received');
    Log::info(json_encode($request->only('data')));;
});
