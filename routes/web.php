<?php

use App\Http\Controllers\ControllerFaceApi;
use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [ControllerFaceApi::class, 'index'])->name('faceapi.index');


Route::get('/pdf/asistencia/{user}/{fecha_inicio}/{fecha_final}', [PdfController::class, 'pdfAsistencia'])->name('pdf.asistencia');