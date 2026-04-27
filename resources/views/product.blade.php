@extends('layouts.app')

@section('title', $product->name . ' — ' . ($site->store_name ?? 'Akhpremium Store'))

@section('content')
<section class="py-8 md:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm text-slate-500 mb-5">
            <a href="{{ route('home') }}" class="hover:text-brand">Home</a>
            <span class="mx-1.5">/</span>
            @if ($product->category)
                <a href="{{ route('home', ['kategori' => $product->category->slug]) }}" class="hover:text-brand">{{ $product->category->name }}</a>
                <span class="mx-1.5">/</span>
            @endif
            <span class="text-slate-700 font-semibold">{{ $product->name }}</span>
        </nav>

        <div class="grid md:grid-cols-2 gap-8 lg:gap-10">
            {{-- Gambar --}}
            <div class="rounded-3xl bg-white border border-slate-200 overflow-hidden shadow-card">
                <div class="aspect-square bg-gradient-to-br from-slate-50 to-slate-100">
                    @if ($product->imageUrl())
                        <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-7xl text-slate-300">🎬</div>
                    @endif
                </div>
            </div>

            {{-- Detail + varian --}}
            <div>
                @if ($product->category)
                    <span class="inline-block uppercase text-xs tracking-widest text-brand font-bold mb-2">{{ $product->category->name }}</span>
                @endif
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">{{ $product->name }}</h1>
                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                    @if ($product->is_auto_send)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-700 px-2 py-0.5 font-semibold">⚡ Auto-Delivery</span>
                    @endif
                    @if ($product->is_best_seller)
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 text-amber-700 px-2 py-0.5 font-semibold">★ Best Seller</span>
                    @endif
                    @if ($product->sold_count)
                        <span>· {{ number_format($product->sold_count, 0, ',', '.') }} terjual</span>
                    @endif
                </div>

                @if ($product->short_description)
                    <p class="mt-4 text-slate-600">{{ $product->short_description }}</p>
                @endif

                {{-- Varian --}}
                <div class="mt-6 rounded-2xl border border-slate-200 bg-white divide-y">
                    <div class="px-5 py-3 text-sm font-bold uppercase tracking-wide text-slate-700">Pilih Paket</div>
                    @forelse ($product->variants as $variant)
                        @php
                            $isOOS = $product->is_auto_send && $variant->available_stocks_count <= 0;
                            $fs = $variant->activeFlashsale();
                            $effective = $variant->effectivePrice();
                        @endphp
                        <div class="px-5 py-4 flex items-center gap-4">
                            <div class="flex-1">
                                <div class="font-semibold flex items-center gap-2">
                                    {{ $variant->name }}
                                    @if ($fs)
                                        <span class="inline-flex items-center text-[10px] font-bold rounded px-1.5 py-0.5 bg-rose-600 text-white">FLASH -{{ $fs->discountPercent() }}%</span>
                                    @endif
                                </div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    @if ($product->is_auto_send)
                                        Stok: {{ $variant->available_stocks_count }} · Auto-delivery
                                    @else
                                        Manual delivery
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                @if ($fs)
                                    <div class="text-rose-600 font-extrabold">Rp {{ number_format($effective, 0, ',', '.') }}</div>
                                    <div class="price-strike">Rp {{ number_format($variant->price, 0, ',', '.') }}</div>
                                @else
                                    <div class="font-extrabold">Rp {{ number_format($effective, 0, ',', '.') }}</div>
                                @endif
                            </div>
                            <div>
                                @if ($isOOS)
                                    <button type="button" disabled class="rounded-xl bg-slate-100 text-slate-400 cursor-not-allowed font-semibold px-4 py-2.5 text-sm">Habis</button>
                                @else
                                    <a href="{{ route('checkout.show', [$product, $variant]) }}"
                                       class="inline-flex rounded-xl btn-brand font-semibold px-4 py-2.5 text-sm">Beli Sekarang</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-sm text-slate-500">Belum ada varian.</div>
                    @endforelse
                </div>

                {{-- Deskripsi panjang --}}
                @if ($product->description)
                    <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-5">
                        <h3 class="font-bold mb-2">Deskripsi</h3>
                        <div class="prose-content text-slate-700 text-sm">{!! nl2br(e($product->description)) !!}</div>
                    </div>
                @endif

                <div class="mt-6 flex items-center gap-4 text-xs text-slate-500">
                    <span class="inline-flex items-center gap-1">🛡️ Garansi penuh</span>
                    <span>·</span>
                    <span class="inline-flex items-center gap-1">💳 Pembayaran aman via Pakasir</span>
                    <span>·</span>
                    <span class="inline-flex items-center gap-1">⚡ Auto delivery</span>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
