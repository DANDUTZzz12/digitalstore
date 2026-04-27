@extends('layouts.app')

@section('title', $product->name . ' — Akhpremium Store')

@section('content')
    <nav class="text-sm text-slate-500 mb-4">
        <a href="{{ route('home') }}" class="hover:text-purple-600">Home</a>
        <span class="mx-2">/</span>
        @if ($product->category)
            <a href="{{ route('home', ['kategori' => $product->category->slug]) }}" class="hover:text-purple-600">
                {{ $product->category->name }}
            </a>
            <span class="mx-2">/</span>
        @endif
        <span class="text-slate-700 font-semibold">{{ $product->name }}</span>
    </nav>

    <div class="grid md:grid-cols-2 gap-8 bg-white rounded-3xl border border-slate-200 p-6 md:p-8 shadow-sm">
        <div class="aspect-square bg-gradient-to-br from-purple-100 to-blue-100 rounded-2xl flex items-center justify-center overflow-hidden">
            @if ($product->image)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($product->image) }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover">
            @else
                <span class="text-6xl">🎬</span>
            @endif
        </div>

        <div>
            @if ($product->category)
                <div class="text-xs font-semibold text-purple-600 uppercase tracking-wide">
                    {{ $product->category->name }}
                </div>
            @endif

            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 mt-1">
                {{ $product->name }}
            </h1>

            @if ($product->description)
                <p class="mt-3 text-slate-600 whitespace-pre-line">{{ $product->description }}</p>
            @endif

            <div class="mt-6">
                <div class="font-bold text-slate-800 mb-2">Pilih paket:</div>

                @if ($product->variants->isEmpty())
                    <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 text-sm">
                        Produk ini belum punya varian. Silakan hubungi admin.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($product->variants as $variant)
                            <div class="flex items-center justify-between bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">
                                <div>
                                    <div class="font-bold text-slate-800">{{ $variant->name }}</div>
                                    <div class="text-sm text-slate-500">
                                        Stok: {{ $variant->available_stocks_count ?? 0 }}
                                        @if ($product->is_auto_send)
                                            <span class="ml-1 text-green-600 font-semibold">&middot; Auto-delivery</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="font-extrabold text-slate-900">
                                        Rp {{ number_format($variant->price, 0, ',', '.') }}
                                    </div>
                                    @if ($product->is_auto_send && ($variant->available_stocks_count ?? 0) <= 0)
                                        <span class="inline-block rounded-xl bg-slate-300 px-4 py-2 text-sm font-bold text-white cursor-not-allowed">
                                            Habis
                                        </span>
                                    @else
                                        <a href="{{ route('checkout.show', [$product, $variant]) }}"
                                           class="inline-block rounded-xl bg-purple-600 hover:bg-purple-700 px-4 py-2 text-sm font-bold text-white transition">
                                            Beli Sekarang
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
