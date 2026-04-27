<?php

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
