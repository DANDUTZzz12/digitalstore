@extends('layouts.app')

@section('title', 'Keranjang')

@section('content')
<section class="py-10 md:py-14">
    <div class="max-w-3xl mx-auto px-4">
        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Keranjang Belanja</h1>
        <p class="text-sm text-slate-500 mt-1">Daftar produk yang ingin kamu beli — pembayaran dilakukan per item.</p>

        @if ($items->isEmpty())
            <div class="mt-6 rounded-2xl bg-white border border-slate-200 p-10 text-center">
                <p class="text-3xl mb-2">🛒</p>
                <p class="text-slate-600">Keranjang kamu masih kosong.</p>
                <a href="{{ route('home') }}" class="inline-block mt-4 text-brand font-semibold hover:underline">Mulai belanja →</a>
            </div>
        @else
            <div class="mt-6 space-y-4">
                @foreach ($items as $item)
                    @php
                        $eff = $item->variant?->effectivePrice() ?? 0;
                        $subtotal = $eff * $item->quantity;
                    @endphp
                    <div class="rounded-2xl bg-white border border-slate-200 p-4 md:p-5 flex flex-col md:flex-row md:items-center gap-4">
                        <div class="flex-1">
                            <div class="font-bold text-slate-900">{{ $item->product?->name ?? '—' }}</div>
                            <div class="text-xs text-slate-500">Paket: {{ $item->variant?->name ?? '—' }}</div>
                            @if ($item->variant?->warranty_days)
                                <span class="inline-flex items-center gap-1 mt-1 rounded-full bg-emerald-50 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">🛡 Garansi {{ $item->variant->warranty_days }} Hari</span>
                            @endif
                            @if ($item->variant?->shareTypeLabel())
                                <span class="inline-flex items-center gap-1 mt-1 rounded-full bg-violet-50 text-violet-700 px-2 py-0.5 text-[10px] font-bold">{{ $item->variant->shareTypeLabel() }}</span>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('cart.update', $item) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <label class="text-xs text-slate-500">Qty</label>
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="10"
                                   class="w-16 rounded-lg border border-slate-200 px-2 py-1 text-sm">
                            <button class="text-xs font-semibold text-brand hover:underline">Update</button>
                        </form>
                        <div class="text-right">
                            <div class="font-extrabold text-slate-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</div>
                            <div class="flex items-center gap-2 mt-1 justify-end">
                                <a href="{{ route('checkout.show', [$item->product, $item->variant]) }}"
                                   class="text-xs font-bold rounded-lg btn-brand px-3 py-1.5">Bayar Sekarang</a>
                                <form method="POST" action="{{ route('cart.destroy', $item) }}">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-rose-600 hover:underline">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <p class="mt-4 text-xs text-slate-500">
                Karena gateway pembayaran (QRIS/VA/E-Wallet) hanya menerima 1 transaksi per pembayaran,
                tiap item dibayar terpisah. Klik <strong>Bayar Sekarang</strong> di item yang ingin diproses.
            </p>
        @endif
    </div>
</section>
@endsection
