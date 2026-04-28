<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PakasirWebhookController;
use Illuminate\Support\Facades\Route;

// ======== Publik (frontend toko) ========
Route::get('/', [FrontController::class, 'index'])->name('home');
Route::get('/produk/{product}', [FrontController::class, 'show'])->name('products.show');

// Halaman info
Route::get('/cara-pemesanan', [FrontController::class, 'howToOrder'])->name('pages.how-to-order');
Route::get('/faq', [FrontController::class, 'faq'])->name('pages.faq');
Route::get('/ketentuan-order', [FrontController::class, 'terms'])->name('pages.terms');
Route::get('/artikel', [FrontController::class, 'articleIndex'])->name('articles.index');
Route::get('/artikel/{article:slug}', [FrontController::class, 'articleShow'])->name('articles.show');
Route::get('/cek-invoice', [FrontController::class, 'cekInvoice'])->name('pages.cek-invoice');

// Checkout instan — tanpa keranjang.
Route::get('/checkout/{product}/{variant}', [CheckoutController::class, 'show'])
    ->name('checkout.show');

Route::post('/checkout', [CheckoutController::class, 'store'])
    ->middleware('throttle:10,1') // maks 10 order/menit per IP
    ->name('checkout.store');

// Halaman invoice publik (akses via order_code random, tidak dapat ditebak).
Route::get('/invoice/{orderCode}', [InvoiceController::class, 'show'])
    ->name('invoice.show');

// Webhook dari Pakasir.
Route::post('/webhooks/pakasir', [PakasirWebhookController::class, 'handle'])
    ->middleware('throttle:120,1')
    ->name('webhooks.pakasir');

// ======== Auth (login / register / lupa password) ========
// Rate limit ketat utk cegah brute-force.
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.attempt');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:3,1')
        ->name('register.attempt');

    Route::get('/lupa-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/lupa-password', [AuthController::class, 'sendResetLink'])
        ->middleware('throttle:2,1')
        ->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:5,1')
        ->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ======== Akun (dashboard + history) ========
Route::middleware('auth')->prefix('akun')->name('account.')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');
    Route::get('/orders', [AccountController::class, 'orders'])->name('orders.index');
    Route::get('/profil', [AccountController::class, 'profile'])->name('profile');
    Route::post('/profil', [AccountController::class, 'updateProfile'])->name('profile.update');
});
