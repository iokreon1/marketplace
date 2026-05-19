<?php

use App\Http\Controllers\StoreBalanceController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('user', UserController::class); // 'user' itu adalah nanti endpointnya
Route::get('user/all/paginated', [UserController::class, 'getAllPaginated']); // getAllPaginated adalah nama methodnya 

Route::apiResource('store', StoreController::class);
Route::get('store/all/paginated', [StoreController::class, 'getAllPaginated']);
Route::post('store/{id}/verified', [StoreController::class, 'updateVerifiedStatus']);

Route::apiResource('store-balance', StoreBalanceController::class)->except(['store', 'update', 'destroy']); // karena store balance itu cuma bisa diambil datanya, jadi saya except store, update, destroy
Route::get('store-balance/all/paginated', [StoreBalanceController::class, 'getAllPaginated']);

// apiResource itu maksudnya seperti ini 
// Daripada kamu nulis:

// Route::get('/user', [UserController::class, 'index']); // ini kepanggil dari GET /user (apiResource)
// Route::post('/user', [UserController::class, 'store']);
// Route::get('/user/{id}', [UserController::class, 'show']);
// Route::put('/user/{id}', [UserController::class, 'update']);
// Route::delete('/user/{id}', [UserController::class, 'destroy']);

// cukup tulis sekali menggunakan apiResource
// kemudian nanti menggunakan route yang mananya tergantung dari kamu menggunakan yg get, atau post dll 