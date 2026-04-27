<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;

// Jalur ke halaman utama etalase
Route::get('/', [FrontController::class, 'index']);