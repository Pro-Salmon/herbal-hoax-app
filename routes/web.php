<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DetectionController;

Route::get('/', [DetectionController::class, 'index'])->name('detection.index');
Route::post('/detect', [DetectionController::class, 'process'])->name('detection.process');