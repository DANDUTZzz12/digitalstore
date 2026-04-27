@extends('layouts.app')

@section('title', 'Checkout — ' . $product->name)

@section('content')
    <div class="max-w-xl mx-auto">
        <h1 class="text-2xl font-extrabold text-slate-900">Checkout</h1>
        <p class="text-slate-500 mt-1">Lengkapi data kontak, lalu lanjut bayar via QRIS / VA / E-Wallet.</p>

        <div class="mt-6 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <div class="text-xs text-purple-600 font-semibold uppercase tracking-wide">
                        {{ $product->category?->name }}
                    </div>
                    <div class="font-bold text-slate-800">{{ $product->name }}</div>
                    <div class="text-sm text-slate-500">Paket: {{ $variant->name }}</div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-slate-400">Total</div>
                    <div class="text-xl font-extrabold text-slate-900">
                        Rp {{ number_format($variant->price, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="mt-4 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('checkout.store') }}" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="product_variant_id" value="{{ $variant->id }}">

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Email (wajib)</label>
                    <input type="email" name="customer_email" required
                           value="{{ old('customer_email') }}"
                           placeholder="akun-digital-kamu@email.com"
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-200">
                    <p class="text-xs text-slate-500 mt-1">Kredensial akun akan dikirim ke email ini (dan tampil di halaman invoice setelah pembayaran).</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Nomor WhatsApp (opsional)</label>
                    <input type="text" name="customer_phone"
                           value="{{ old('customer_phone') }}"
                           placeholder="08xxxxxxxxxx"
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-200">
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-base px-4 py-3 transition">
                    Bayar Sekarang
                </button>

                <p class="text-xs text-slate-500 text-center">
                    Kamu akan diarahkan ke halaman pembayaran Pakasir yang aman.
                </p>
            </form>
        </div>
    </div>
@endsection
