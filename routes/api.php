<?php

use App\Http\Controllers\ControllerFaceApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/images', [ControllerFaceApi::class, 'show']);
Route::get('/get-labels', [ControllerFaceApi::class, 'getLabels']);
Route::get('/get-user-id', [ControllerFaceApi::class, 'getUser']);
Route::get('/checkentrada', [ControllerFaceApi::class, 'checkhoraentrada']);
Route::get('/checksalida', [ControllerFaceApi::class, 'checkhorasalida']);
Route::post('/registroentrado', [ControllerFaceApi::class, 'registrarEntrada']);
Route::post('/registrosalida', [ControllerFaceApi::class, 'registrarSalida']);
Route::get('/horarios', [ControllerFaceApi::class, 'getHorarios']);