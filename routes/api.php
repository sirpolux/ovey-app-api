<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TransactionController;
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
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/transactions', [TransactionController::class, 'index']);

    Route::get('/clients', [ClientController::class, 'index']);
    Route::post('transactions/bulk', [TransactionController::class, 'storeMultipleTransaction']);
    Route::get('/transactions/client', [TransactionController::class, 'getUserTransaction']);

    Route::get('/accounts',[AccountController::class, 'index']);

    
    
});
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);


// Route::get('/clients', [ClientController::class, 'index']);
// Route::post('/clients',[ClientController::class, 'store']);
