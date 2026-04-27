<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pakasir Payment Gateway
    |--------------------------------------------------------------------------
    | Docs: https://pakasir.com/p/docs
    |
    | project  : slug project di dashboard Pakasir
    | api_key  : API Key dari project tersebut
    | qris_only: batasi metode pembayaran hanya QRIS (true) atau semua (false)
    */

    'project' => env('PAKASIR_PROJECT'),

    'api_key' => env('PAKASIR_API_KEY'),

    'qris_only' => env('PAKASIR_QRIS_ONLY', false),

    'base_url' => env('PAKASIR_BASE_URL', 'https://app.pakasir.com'),

    /*
    | Default masa berlaku order sebelum otomatis expired (menit).
    */
    'order_expiry_minutes' => (int) env('PAKASIR_ORDER_EXPIRY_MINUTES', 60),

];
