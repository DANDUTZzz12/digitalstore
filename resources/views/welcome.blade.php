@extends('layouts.app')

@section('title', 'Akhpremium Store — Jual Akun Premium Terpercaya')

@section('content')
    <div class="bg-gradient-to-r from-purple-100 via-white to-blue-100 rounded-3xl p-6 md:p-8 relative shadow-sm border border-purple-200">
        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800">
            Akun Premium Legal, Harga Ramah.
        </h1>
        <p class="mt-2 text-slate-600 font-medium">
            Proses otomatis — pilih produk, bayar QRIS, akun langsung dikirim.
        </p>

        <form action="{{ route('home') }}" method="GET" class="mt-6 flex gap-2 max-w-xl">
            <input
                type="text"
                name="q"
                value="{{ $searchQuery }}"
                placeholder="Cari produk (Netflix, CapCut, Spotify...)"
                class="flex-1 rounded-xl border border-purple-200 bg-white px-4 py-3 text-sm focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-200"
            >
            <button type="submit" class="rounded-xl bg-purple-600 px-5 py-3 text-sm font-bold text-white hover:bg-purple-700 transition">
                Cari
            </button>
        </form>
    </div>

    <section id="katalog" class="mt-10">
        <div class="flex flex-wrap items-center gap-2 mb-6">
            <a href="{{ route('home') }}"
               class="px-4 py-2 rounded-full text-sm font-bold transition
                      {{ ! $activeCategory ? 'bg-purple-600 text-white' : 'bg-white text-slate-700 border border-slate-200 hover:border-purple-300' }}">
                Semua
            </a>
            @foreach ($categories as $category)
                <a href="{{ route('home', ['kategori' => $category->slug]) }}"
                   class="px-4 py-2 rounded-full text-sm font-bold transition
                          {{ $activeCategory === $category->slug ? 'bg-purple-600 text-white' : 'bg-white text-slate-700 border border-slate-200 hover:border-purple-300' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>

        @if ($products->isEmpty())
            <div class="bg-white border border-dashed border-slate-200 rounded-2xl p-10 text-center text-slate-500">
                Belum ada produk yang tersedia. Admin bisa menambahkan produk lewat
                <a href="{{ url('/admin') }}" class="text-purple-600 font-bold underline">panel admin</a>.
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($products as $product)
                    @php
                        $minPrice = $product->variants->min('price') ?? $product->price;
                    @endphp
                    <a href="{{ route('products.show', $product) }}"
                       class="group block bg-white rounded-2xl border border-slate-200 hover:border-purple-300 hover:shadow-lg transition p-4">
                        <div class="aspect-square bg-gradient-to-br from-purple-100 to-blue-100 rounded-xl mb-3 flex items-center justify-center overflow-hidden">
                            @if ($product->image)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($product->image) }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition">
                            @else
                                <span class="text-3xl">🎬</span>
                            @endif
                        </div>
                        <div class="text-xs font-semibold text-purple-600 uppercase tracking-wide">
                            {{ $product->category?->name ?? 'Produk' }}
                        </div>
                        <div class="font-bold text-slate-800 mt-1 line-clamp-2">
                            {{ $product->name }}
                        </div>
                        <div class="mt-2 text-sm text-slate-500">Mulai dari</div>
                        <div class="text-lg font-extrabold text-slate-900">
                            Rp {{ number_format($minPrice, 0, ',', '.') }}
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
@endsection
