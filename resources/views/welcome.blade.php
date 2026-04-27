@extends('layouts.app')

@section('content')

{{-- HERO --}}
<section class="gradient-hero text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 grid md:grid-cols-2 gap-10 items-center">
        <div>
            <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wider bg-white/10 backdrop-blur border border-white/20 rounded-full px-3 py-1 mb-5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                Auto-Delivery 24 Jam
            </span>
            <h1 class="text-3xl md:text-5xl font-extrabold leading-tight mb-4">
                {{ $site->hero_title ?? 'Akun Premium Legal, Harga Ramah.' }}
            </h1>
            <p class="text-white/80 max-w-xl text-base md:text-lg">
                {{ $site->hero_subtitle ?? 'Pilih produk, bayar QRIS / VA / E-Wallet, akun langsung dikirim ke email kamu. Tanpa cart, tanpa ribet.' }}
            </p>
            <form action="{{ route('home') }}#katalog" method="GET" class="mt-6 flex max-w-lg bg-white rounded-2xl p-1.5 shadow-lg shadow-black/20">
                <input type="text" name="q" value="{{ $searchQuery ?? '' }}"
                       placeholder="Cari Netflix, Spotify, CapCut..."
                       class="flex-1 bg-transparent text-slate-900 placeholder-slate-400 px-4 py-3 outline-none text-sm rounded-l-xl">
                <button type="submit" class="rounded-xl btn-brand font-semibold px-5 py-3 text-sm">Cari Produk</button>
            </form>
            <div class="mt-6 flex items-center gap-6 text-sm text-white/70">
                <div class="flex items-center gap-2"><span class="text-emerald-400">●</span> 100% Legal</div>
                <div class="flex items-center gap-2"><span class="text-emerald-400">●</span> Garansi Penuh</div>
                <div class="flex items-center gap-2"><span class="text-emerald-400">●</span> Pembayaran Aman</div>
            </div>
        </div>
        <div class="hidden md:block relative">
            <div class="absolute -top-4 -left-4 w-32 h-32 bg-fuchsia-500/30 rounded-3xl blur-2xl"></div>
            <div class="relative grid grid-cols-2 gap-3">
                @foreach ($products->take(4) as $p)
                    <div class="rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 p-4">
                        <div class="aspect-[4/3] rounded-xl bg-gradient-to-br from-white/20 to-white/5 flex items-center justify-center overflow-hidden">
                            @if ($p->imageUrl())
                                <img src="{{ $p->imageUrl() }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-3xl">🎬</span>
                            @endif
                        </div>
                        <div class="mt-2 text-xs uppercase tracking-wider opacity-80">{{ optional($p->category)->name }}</div>
                        <div class="font-semibold leading-tight text-sm">{{ \Illuminate\Support\Str::limit($p->name, 28) }}</div>
                        <div class="text-emerald-300 font-bold text-sm">Rp {{ number_format($p->lowestPrice(), 0, ',', '.') }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- FLASH SALE --}}
@if ($flashsales->isNotEmpty())
    @php $earliestEnd = $flashsales->min('end_at'); @endphp
    <section class="-mt-10 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl bg-gradient-to-r from-rose-500 via-orange-500 to-amber-400 p-1 shadow-2xl">
                <div class="rounded-[1.4rem] bg-white p-5 md:p-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">⚡</span>
                            <div>
                                <h2 class="font-extrabold text-2xl md:text-3xl bg-gradient-to-r from-rose-600 to-orange-500 bg-clip-text text-transparent">FLASH SALE</h2>
                                <p class="text-sm text-slate-500">Promo terbatas! Buruan sebelum kehabisan.</p>
                            </div>
                        </div>
                        <div id="flashsale-countdown" class="font-mono text-base md:text-xl font-bold text-rose-600 flex items-center gap-2">
                            <span class="text-xs uppercase text-slate-500 mr-2">Berakhir dalam</span>
                            <span id="fs-h" class="px-2 py-1 rounded bg-slate-900 text-white">00</span>:
                            <span id="fs-m" class="px-2 py-1 rounded bg-slate-900 text-white">00</span>:
                            <span id="fs-s" class="px-2 py-1 rounded bg-slate-900 text-white">00</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                        @foreach ($flashsales as $fs)
                            @php $variant = $fs->variant; $product = $variant?->product; @endphp
                            @if ($variant && $product)
                                <a href="{{ route('checkout.show', [$product, $variant]) }}"
                                   class="group rounded-2xl border border-slate-200 hover:border-brand bg-white overflow-hidden transition shadow-card hover:shadow-soft">
                                    <div class="relative aspect-square bg-gradient-to-br from-slate-50 to-slate-200 overflow-hidden">
                                        @if ($product->imageUrl())
                                            <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-3xl text-slate-400">🎬</div>
                                        @endif
                                        <span class="absolute top-2 left-2 inline-flex items-center text-xs font-bold rounded-md px-2 py-1 bg-rose-600 text-white">-{{ $fs->discountPercent() }}%</span>
                                    </div>
                                    <div class="p-3">
                                        <div class="text-xs uppercase tracking-wider text-brand font-semibold mb-1">{{ optional($product->category)->name }}</div>
                                        <div class="font-semibold text-sm leading-tight line-clamp-2 mb-1">{{ $product->name }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $variant->name }}</div>
                                        <div class="mt-1.5 flex items-baseline gap-2">
                                            <span class="font-bold text-rose-600 text-sm">Rp {{ number_format($fs->flash_price, 0, ',', '.') }}</span>
                                            <span class="price-strike">Rp {{ number_format($variant->price, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="mt-2 text-xs text-slate-500">Sisa <strong>{{ $fs->remaining() }}</strong> pcs</div>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        (function () {
            var endAt = new Date('{{ \Carbon\Carbon::parse($earliestEnd)->toIso8601String() }}').getTime();
            var h = document.getElementById('fs-h');
            var m = document.getElementById('fs-m');
            var s = document.getElementById('fs-s');
            function pad(n){ return n < 10 ? '0'+n : ''+n; }
            function tick() {
                var now = Date.now();
                var diff = Math.max(0, Math.floor((endAt - now)/1000));
                var hours = Math.floor(diff / 3600);
                var mins = Math.floor((diff % 3600) / 60);
                var secs = diff % 60;
                h.textContent = pad(hours);
                m.textContent = pad(mins);
                s.textContent = pad(secs);
                if (diff <= 0) { clearInterval(t); }
            }
            tick();
            var t = setInterval(tick, 1000);
        })();
    </script>
    @endpush
@endif

{{-- WHY US --}}
<section class="py-12 md:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-4 gap-4">
        @php
            $features = [
                ['icon'=>'⚡','title'=>'Auto Delivery','desc'=>'Akun otomatis terkirim setelah pembayaran berhasil.'],
                ['icon'=>'🛡️','title'=>'Garansi Penuh','desc'=>'Jika akun bermasalah, kami ganti tanpa ribet.'],
                ['icon'=>'💳','title'=>'Pembayaran Aman','desc'=>'QRIS, VA & E-Wallet via Pakasir terenkripsi.'],
                ['icon'=>'💬','title'=>'Live Chat 24/7','desc'=>'Tim support siap bantu via WhatsApp.'],
            ];
        @endphp
        @foreach ($features as $f)
            <div class="rounded-2xl bg-white border border-slate-200 p-5 hover:shadow-card transition">
                <div class="text-2xl">{{ $f['icon'] }}</div>
                <div class="mt-2 font-bold">{{ $f['title'] }}</div>
                <div class="text-sm text-slate-500 mt-1">{{ $f['desc'] }}</div>
            </div>
        @endforeach
    </div>
</section>

{{-- KATALOG --}}
<section id="katalog" class="pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-6">
            <div>
                <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight">Katalog Produk</h2>
                <p class="text-slate-500 text-sm mt-1">Pilih produk premium yang kamu butuhkan.</p>
            </div>
        </div>

        <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-1">
            <a href="{{ route('home') }}"
               class="shrink-0 rounded-full text-sm font-semibold px-4 py-2 transition
                      {{ ! $activeCategory ? 'btn-brand' : 'bg-white border border-slate-200 hover:border-brand text-slate-700' }}">
                SEMUA
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('home', ['kategori' => $cat->slug]) }}"
                   class="shrink-0 rounded-full text-sm font-semibold px-4 py-2 uppercase tracking-wide transition
                          {{ $activeCategory === $cat->slug ? 'btn-brand' : 'bg-white border border-slate-200 hover:border-brand text-slate-700' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        @if ($products->isEmpty())
            <div class="text-center py-16 bg-white rounded-2xl border border-dashed border-slate-300">
                <div class="text-5xl mb-3">🛒</div>
                <p class="text-slate-500">Belum ada produk yang cocok dengan pencarian/filter kamu.</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-5">
                @foreach ($products as $product)
                    @php
                        $stockSum = $product->variants->sum(fn ($v) => $v->stocks()->where('is_sold', false)->count());
                        $lowest = $product->lowestPrice();
                        $firstVariant = $product->variants->first();
                        $fs = $firstVariant?->activeFlashsale();
                    @endphp
                    <article class="group rounded-2xl bg-white border border-slate-200 overflow-hidden hover:shadow-soft hover:-translate-y-0.5 transition">
                        <a href="{{ route('products.show', $product) }}" class="block">
                            <div class="relative aspect-square bg-gradient-to-br from-slate-50 to-slate-200 overflow-hidden">
                                @if ($product->imageUrl())
                                    <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-4xl text-slate-400">🎬</div>
                                @endif
                                @if ($product->is_best_seller)
                                    <span class="absolute top-2 right-2 text-xs font-bold rounded-md px-2 py-1 bg-amber-400 text-amber-900">★ Best Seller</span>
                                @endif
                                @if ($fs)
                                    <span class="absolute top-2 left-2 inline-flex items-center text-xs font-bold rounded-md px-2 py-1 bg-rose-600 text-white">FLASH -{{ $fs->discountPercent() }}%</span>
                                @endif
                                <span class="absolute bottom-2 left-2 inline-flex items-center text-[10px] font-semibold rounded px-1.5 py-0.5 bg-slate-900/80 text-white">
                                    Stok: {{ $stockSum }}
                                </span>
                            </div>
                        </a>
                        <div class="p-3.5">
                            <div class="text-[10px] uppercase tracking-wider text-brand font-bold mb-1">{{ optional($product->category)->name }}</div>
                            <a href="{{ route('products.show', $product) }}" class="block">
                                <h3 class="font-semibold leading-tight text-sm line-clamp-2 mb-1.5 group-hover:text-brand">{{ $product->name }}</h3>
                            </a>
                            @if ($product->short_description)
                                <p class="text-xs text-slate-500 line-clamp-2 mb-2">{{ $product->short_description }}</p>
                            @endif
                            <div class="flex items-baseline gap-1.5 mb-3">
                                @if ($fs)
                                    <span class="text-base font-extrabold text-rose-600">Rp {{ number_format($fs->flash_price, 0, ',', '.') }}</span>
                                    <span class="price-strike">Rp {{ number_format($firstVariant->price, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-xs text-slate-400">Mulai</span>
                                    <span class="text-base font-extrabold text-slate-900">Rp {{ number_format($lowest, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('products.show', $product) }}"
                                   class="flex-1 text-center rounded-xl btn-brand font-semibold text-xs py-2.5">
                                    BELI
                                </a>
                                <a href="{{ route('products.show', $product) }}"
                                   class="rounded-xl border border-slate-200 hover:border-brand text-slate-600 hover:text-brand text-xs py-2.5 px-3">
                                    Detail
                                </a>
                            </div>
                            @if ($product->sold_count > 0)
                                <div class="mt-2 text-[10px] text-slate-400">{{ number_format($product->sold_count, 0, ',', '.') }} terjual</div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ARTIKEL --}}
@if ($articles->isNotEmpty())
    <section class="pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-6">
                <div>
                    <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight">Artikel & Tips Terbaru</h2>
                    <p class="text-slate-500 text-sm mt-1">Update info terbaru seputar akun premium.</p>
                </div>
                <a href="{{ route('articles.index') }}" class="text-sm font-semibold text-brand hover:underline">Lihat Semua →</a>
            </div>
            <div class="grid md:grid-cols-3 gap-5">
                @foreach ($articles as $article)
                    <a href="{{ route('articles.show', $article) }}" class="block rounded-2xl bg-white border border-slate-200 overflow-hidden hover:shadow-soft transition group">
                        <div class="aspect-video bg-gradient-to-br from-slate-100 to-slate-200">
                            @if ($article->cover_image)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($article->cover_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-4xl text-slate-400">📝</div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold leading-snug line-clamp-2 group-hover:text-brand mb-2">{{ $article->title }}</h3>
                            @if ($article->excerpt)
                                <p class="text-sm text-slate-500 line-clamp-3">{{ $article->excerpt }}</p>
                            @endif
                            @if ($article->published_at)
                                <p class="text-xs text-slate-400 mt-3">{{ $article->published_at->translatedFormat('d M Y') }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif

@endsection
