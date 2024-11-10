<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Route::resource('clients', ClientController::class);

Route::get('/clients/search/{keyword}', [ClientController::class, 'search']);

Route::group(['middleware'=>['auth:sanctum']], function(){
    Route::get('/clients/search/{keyword}', [ClientController::class, 'search']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/clients', [ClientController::class, 'store']);

    
    
});

Route::post('/auth/register', [AuthController::class, 'register']);


// Route::get('/clients', [ClientController::class, 'index']);
// Route::post('/clients',[ClientController::class, 'store']);
