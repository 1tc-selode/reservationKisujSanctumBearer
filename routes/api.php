<?php

use App\Http\Controllers\Api\ReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
//nyilvános
Route::get('/hello', function (Request $request) {
    return response()->json(['message'=>'Hello API']);
});
Route::post('/register',[AuthController::class,'Register']); //regisztráció
Route::post('/login',[AuthController::class,'Login']); //regisztráció


//authenticated routes
/*
Route::middleware('auth:sanctum')->post('/logout',[AuthController::class,'Logout']); //logout

Route::middleware('auth:sanctum')->get('/reservations', [ReservationController::class, 'index']); // összes foglalás
Route::middleware('auth:sanctum')->get('/reservations/{id}', [ReservationController::class, 'show']);
Route::middleware('auth:sanctum')->post('/reservations', [ReservationController::class, 'store']);
Route::middleware('auth:sanctum')->put('/reservations/{id}', [ReservationController::class, 'update']);
Route::middleware('auth:sanctum')->patch('/reservations/{id}', [ReservationController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/reservations/{id}', [ReservationController::class, 'destroy']);
*/

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout',[AuthController::class,'Logout']);
    Route::apiResource('reservations', ReservationController::class);
});
