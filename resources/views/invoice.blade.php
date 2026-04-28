@extends('layouts.app')

@section('title', 'Invoice ' . $order->order_code)

@push('head')
    @if ($order->isPending())
        <meta http-equiv="refresh" content="15">
    @endif
@endpush

@section('content')
<section class="py-10 md:py-14">
    <div class="max-w-xl mx-auto px-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-card">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <div class="text-xs text-slate-400 font-semibold uppercase tracking-wide">Invoice</div>
                    <div class="font-extrabold text-lg text-slate-900">{{ $order->order_code }}</div>
                </div>
                @php
                    $color = match ($order->status) {
                        \App\Models\Order::STATUS_PAID => 'bg-green-100 text-green-800',
                        \App\Models\Order::STATUS_PENDING => 'bg-amber-100 text-amber-800',
                        \App\Models\Order::STATUS_FAILED, \App\Models\Order::STATUS_EXPIRED, \App\Models\Order::STATUS_CANCELLED => 'bg-red-100 text-red-800',
                        default => 'bg-slate-100 text-slate-800',
                    };
                    $label = match ($order->status) {
                        \App\Models\Order::STATUS_PAID => 'PAID',
                        \App\Models\Order::STATUS_PENDING => 'PENDING',
                        \App\Models\Order::STATUS_FAILED => 'FAILED',
                        \App\Models\Order::STATUS_EXPIRED => 'EXPIRED',
                        \App\Models\Order::STATUS_CANCELLED => 'CANCELLED',
                        \App\Models\Order::STATUS_REFUNDED => 'REFUNDED',
                        default => strtoupper($order->status),
                    };
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $color }}">
                    {{ $label }}
                </span>
            </div>

            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-slate-500">Produk</dt>
                    <dd class="font-semibold text-slate-800">{{ $order->product?->name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Paket</dt>
                    <dd class="font-semibold text-slate-800">{{ $order->variant?->name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Email pembeli</dt>
                    <dd class="font-semibold text-slate-800">{{ $order->customer_email }}</dd>
                </div>
                <div class="flex justify-between border-t border-slate-100 pt-2 mt-2">
                    <dt class="text-slate-500">Total bayar</dt>
                    <dd class="font-extrabold text-slate-900">
                        Rp {{ number_format($order->total_payment, 0, ',', '.') }}
                    </dd>
                </div>
                @if ($order->payment_method)
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Metode</dt>
                        <dd class="font-semibold text-slate-800 uppercase">{{ $order->payment_method }}</dd>
                    </div>
                @endif
                @if ($order->paid_at)
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Dibayar</dt>
                        <dd class="font-semibold text-slate-800">{{ $order->paid_at->format('d M Y H:i') }}</dd>
                    </div>
                @endif
            </dl>

            @if ($order->isPending())
                <div class="mt-6 bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800">
                    <div class="font-bold">Menunggu pembayaran...</div>
                    <div class="mt-1">Halaman ini akan refresh otomatis setiap 15 detik. Jika sudah bayar tapi status belum berubah, tunggu beberapa detik lagi.</div>
                </div>
            @endif

            @if ($order->isPaid())
                @if ($credentials)
                    <div class="mt-6 bg-green-50 border border-green-200 rounded-xl p-4">
                        <div class="font-extrabold text-green-900 mb-2">🎉 Akun kamu siap dipakai!</div>
                        <div class="space-y-2 text-sm">
                            <div>
                                <div class="text-xs text-green-700 font-semibold uppercase">Email / No HP</div>
                                <code class="block bg-white border border-green-200 rounded-lg px-3 py-2 mt-1 select-all">{{ $credentials['email_or_phone'] }}</code>
                            </div>
                            <div>
                                <div class="text-xs text-green-700 font-semibold uppercase">Password</div>
                                <code class="block bg-white border border-green-200 rounded-lg px-3 py-2 mt-1 select-all">{{ $credentials['password'] }}</code>
                            </div>
                            @if (! empty($credentials['additional_info']))
                                <div>
                                    <div class="text-xs text-green-700 font-semibold uppercase">Info tambahan</div>
                                    <pre class="bg-white border border-green-200 rounded-lg px-3 py-2 mt-1 text-xs whitespace-pre-wrap">{{ $credentials['additional_info'] }}</pre>
                                </div>
                            @endif
                        </div>
                        <p class="mt-3 text-xs text-green-800">
                            Simpan halaman ini atau bookmark URL invoice ini untuk akses kredensial di lain waktu.
                        </p>
                    </div>
                @else
                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-900">
                        <div class="font-bold">Pembayaran diterima!</div>
                        <div class="mt-1">Akun akan dikirim oleh admin dalam waktu dekat karena stok otomatis sedang kosong. Terima kasih atas kesabarannya.</div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</section>
@endsection
