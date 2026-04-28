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

            {{-- Tombol kontak admin via WhatsApp dengan auto-prefill order code.
                 Selalu tampil supaya user bisa konfirmasi/komplain kapan saja. --}}
            @php
                $waNumber = preg_replace('/[^0-9]/', '', (string) ($site?->wa_number ?? ''));
                if ($waNumber !== '' && $waNumber[0] === '0') {
                    $waNumber = '62'.substr($waNumber, 1);
                }
                $waText = 'Halo admin, saya telah melakukan pembelian dengan ID order *'.$order->order_code.'*. Mohon dibantu ya.';
                $waUrl = $waNumber !== ''
                    ? 'https://wa.me/'.$waNumber.'?text='.rawurlencode($waText)
                    : null;
            @endphp
            @if ($waUrl)
                <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                   class="mt-6 w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold px-4 py-3 text-sm shadow transition">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163a11.867 11.867 0 0 1-1.587-5.946C.16 5.335 5.495 0 12.05 0a11.817 11.817 0 0 1 8.413 3.488 11.824 11.824 0 0 1 3.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 0 1-5.688-1.448L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.149-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.71.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    Hubungi Admin via WhatsApp
                </a>
                <p class="text-xs text-slate-500 text-center mt-2">
                    Pesan akan otomatis terisi dengan ID order kamu.
                </p>
            @endif
        </div>
    </div>
</section>
@endsection
