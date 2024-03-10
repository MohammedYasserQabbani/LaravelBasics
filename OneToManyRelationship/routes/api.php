<?php

use App\Http\Controllers\AuthorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('/index',[AuthorController::class,'index']);
Route::get('/show/{id}',[AuthorController::class,'show']);
Route::post('/store',[AuthorController::class,'store']);
Route::post('/update/{id}',[AuthorController::class,'update']);
Route::delete('/destroy/{id}',[AuthorController::class,'destroy']);
