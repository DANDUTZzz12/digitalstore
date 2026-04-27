@extends('layouts.app')
@section('title', 'Cek Invoice — ' . ($site->store_name ?? 'Akhpremium Store'))

@section('content')
<section class="py-12 md:py-16">
    <div class="max-w-xl mx-auto px-4">
        <h1 class="text-2xl md:text-3xl font-extrabold text-center">Cek Status Invoice</h1>
        <p class="text-slate-500 text-center mt-1 text-sm">Masukkan kode order untuk melihat status pembayaran & kredensial akun.</p>

        <form method="GET" action="{{ route('pages.cek-invoice') }}" class="mt-6 bg-white rounded-2xl border border-slate-200 p-6 shadow-card">
            @if (! empty($error))
                <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl px-4 py-3 text-sm">{{ $error }}</div>
            @endif
            <label class="block text-sm font-bold text-slate-700 mb-1">Kode Order</label>
            <input type="text" name="order_code" required value="{{ $code }}"
                   placeholder="AKH-20260427-XXXXXX"
                   class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-brand focus:outline-none focus:ring-2 ring-brand/30 font-mono">
            <p class="text-xs text-slate-500 mt-1">Kode order kamu terima setelah checkout (cek di email atau halaman invoice).</p>
            <button type="submit" class="mt-4 w-full rounded-xl btn-brand font-extrabold px-4 py-3 text-base">Cek Invoice</button>
        </form>
    </div>
</section>
@endsection
