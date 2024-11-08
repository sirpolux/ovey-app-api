<?php

use App\Http\Controllers\ClientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Client;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::resource('clients', ClientController::class);

// Route::get('/clients', [ClientController::class, 'index']);
// Route::post('/clients',[ClientController::class, 'store']);
