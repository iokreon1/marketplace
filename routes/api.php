<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

route::apiResource('user', UserController::class); // 'user' itu adalah nanti endpointnya
Route::get('user/all/paginated', [UserController::class, 'getAllPaginated']); // getAllPaginated adalah nama methodnya 

// apiResource itu maksudnya seperti ini 
// Daripada kamu nulis:

// Route::get('/user', [UserController::class, 'index']); // ini kepanggil dari GET /user (apiResource)
// Route::post('/user', [UserController::class, 'store']);
// Route::get('/user/{id}', [UserController::class, 'show']);
// Route::put('/user/{id}', [UserController::class, 'update']);
// Route::delete('/user/{id}', [UserController::class, 'destroy']);

// cukup tulis sekali menggunakan apiResource
// kemudian nanti menggunakan route yang mananya tergantung dari kamu menggunakan yg get, atau post dll 