<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
