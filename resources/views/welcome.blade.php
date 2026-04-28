@extends('layouts.app')

@push('head')
<style>
    /* Animasi gradient + blob khusus homepage. Ringan, GPU-friendly. */
    @keyframes blob-drift {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(30px, -20px) scale(1.05); }
        66% { transform: translate(-20px, 30px) scale(0.95); }
    }
    @keyframes shine {
        0% { background-position: -200% center; }
        100% { background-position: 200% center; }
    }
    .blob { animation: blob-drift 14s ease-in-out infinite; will-change: transform; }
    .blob.delay-1 { animation-delay: -4s; }
    .blob.delay-2 { animation-delay: -8s; }

    .hero-shine {
        background: linear-gradient(110deg, #fff 30%, color-mix(in oklab, var(--accent) 60%, white) 50%, #fff 70%);
        background-size: 200% auto;
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        animation: shine 6s linear infinite;
    }
    .glass {
        background: rgba(255,255,255,0.08);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1px solid rgba(255,255,255,0.16);
    }
    .product-card { transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease; }
    .product-card:hover { transform: translateY(-4px); }
    .product-img { transition: transform .5s ease; }
    .product-card:hover .product-img { transform: scale(1.06); }

    .marquee {
        display: flex;
        gap: 3rem;
        animation: marquee 28s linear infinite;
    }
    @keyframes marquee {
        from { transform: translateX(0); }
        to { transform: translateX(-50%); }
    }

    .grid-bg {
        background-image:
            linear-gradient(rgba(255,255,255,0.06) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.06) 1px, transparent 1px);
        background-size: 48px 48px;
        mask-image: radial-gradient(ellipse at center, #000 30%, transparent 75%);
        -webkit-mask-image: radial-gradient(ellipse at center, #000 30%, transparent 75%);
    }

    .badge-flash {
        background: linear-gradient(135deg, #f43f5e, #f97316);
        box-shadow: 0 6px 18px -6px rgba(244,63,94,.6);
    }
    .countdown-chip {
        background: linear-gradient(180deg, #0f172a, #1e293b);
        border: 1px solid rgba(255,255,255,.08);
    }
    .step-line {
        background-image: linear-gradient(90deg, color-mix(in oklab, var(--brand) 60%, transparent), color-mix(in oklab, var(--accent) 60%, transparent));
    }
</style>
@endpush

@section('content')

{{-- ================= HERO ================= --}}
<section class="relative overflow-hidden gradient-hero text-white">
    <div class="absolute inset-0 grid-bg pointer-events-none"></div>
    <div class="absolute -top-32 -left-24 w-[420px] h-[420px] rounded-full blob bg-fuchsia-500/30 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-20 w-[520px] h-[520px] rounded-full blob delay-1 bg-cyan-400/20 blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/3 right-1/3 w-72 h-72 rounded-full blob delay-2 bg-indigo-400/20 blur-3xl pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-20 md:pt-20 md:pb-28 grid lg:grid-cols-2 gap-10 items-center">
        <div>
            <div class="flex items-center gap-2 mb-6">
                <span class="inline-flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider glass rounded-full px-3 py-1.5">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75 animate-ping"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                    </span>
                    Online · Auto-Delivery 24 Jam
                </span>
                <span class="hidden sm:inline-flex items-center gap-1 text-[11px] font-semibold uppercase tracking-wider glass rounded-full px-3 py-1.5">
                    <svg class="w-3.5 h-3.5 text-amber-400" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.39 4.84L20 8l-4 3.9.94 5.46L12 14.77 7.06 17.36 8 11.9 4 8l5.61-1.16L12 2z"/></svg>
                    Rating 4.9/5
                </span>
            </div>

            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-[1.05] tracking-tight">
                {!! \Illuminate\Support\Str::of($site?->hero_title ?? 'Akun Premium Legal, Harga Sahabat.')
                    ->replaceMatches('/(Premium|Legal|Sahabat|Murah|Garansi)/i', '<span class="hero-shine">$1</span>') !!}
            </h1>
            <p class="text-white/80 text-base md:text-lg mt-5 max-w-xl">
                {{ $site?->hero_subtitle ?? 'Pilih produk, bayar QRIS / VA / E-Wallet, akun langsung dikirim ke email kamu. Tanpa cart, tanpa ribet — proses gak sampai 1 menit.' }}
            </p>

            <form action="{{ route('home') }}#katalog" method="GET" class="mt-7 flex max-w-xl glass rounded-2xl p-1.5">
                <div class="flex items-center pl-3 text-white/60">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                </div>
                <input type="text" name="q" value="{{ $searchQuery ?? '' }}"
                       placeholder="Cari Netflix, Spotify, CapCut, ChatGPT..."
                       class="flex-1 bg-transparent text-white placeholder-white/50 px-3 py-3 outline-none text-sm">
                <button type="submit" class="rounded-xl btn-brand font-semibold px-5 py-3 text-sm shadow-soft">Cari</button>
            </form>

            <div class="mt-7 flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-white/85">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-300 grid place-items-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg>
                    </span>
                    100% Legal
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-300 grid place-items-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </span>
                    Garansi Penuh
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-300 grid place-items-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M2 10h20"/></svg>
                    </span>
                    QRIS · VA · E-Wallet
                </div>
            </div>
        </div>

        {{-- Floating product cards stack --}}
        <div class="hidden lg:block relative h-[420px]">
            @php $heroProducts = $products->take(4)->values(); @endphp
            @foreach ($heroProducts as $i => $p)
                @php
                    $positions = [
                        ['top'=>'0','right'=>'40px','rotate'=>'-6deg','z'=>30],
                        ['top'=>'40px','right'=>'220px','rotate'=>'4deg','z'=>20],
                        ['top'=>'200px','right'=>'10px','rotate'=>'2deg','z'=>10],
                        ['top'=>'230px','right'=>'200px','rotate'=>'-3deg','z'=>5],
                    ];
                    $pos = $positions[$i] ?? $positions[0];
                @endphp
                <a href="{{ route('products.show', $p) }}"
                   style="top: {{ $pos['top'] }}; right: {{ $pos['right'] }}; transform: rotate({{ $pos['rotate'] }}); z-index: {{ $pos['z'] }};"
                   class="absolute w-56 glass rounded-2xl p-3 hover:scale-105 hover:rotate-0 transition-all duration-300">
                    <div class="aspect-[4/3] rounded-xl bg-white/5 overflow-hidden grid place-items-center">
                        @if ($p->imageUrl())
                            <img src="{{ $p->imageUrl() }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-3xl">🎬</span>
                        @endif
                    </div>
                    <div class="mt-2.5">
                        <div class="text-[10px] uppercase tracking-wider opacity-70 font-semibold">{{ optional($p->category)->name }}</div>
                        <div class="font-semibold text-sm leading-tight line-clamp-1">{{ $p->name }}</div>
                        <div class="text-emerald-300 font-bold text-sm mt-0.5">Mulai Rp {{ number_format($p->lowestPrice(), 0, ',', '.') }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Stats strip --}}
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-10">
        <div class="glass rounded-2xl px-5 py-4 grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-2 divide-x divide-white/10">
            <div class="text-center px-2">
                <div class="text-xl md:text-2xl font-extrabold">{{ number_format($stats['orders'], 0, ',', '.') }}+</div>
                <div class="text-[11px] uppercase tracking-wider text-white/60">Akun Terkirim</div>
            </div>
            <div class="text-center px-2">
                <div class="text-xl md:text-2xl font-extrabold">{{ $stats['products'] }}+</div>
                <div class="text-[11px] uppercase tracking-wider text-white/60">Produk Premium</div>
            </div>
            <div class="text-center px-2">
                <div class="text-xl md:text-2xl font-extrabold">{{ $stats['satisfaction'] }}%</div>
                <div class="text-[11px] uppercase tracking-wider text-white/60">Kepuasan</div>
            </div>
            <div class="text-center px-2">
                <div class="text-xl md:text-2xl font-extrabold">24/7</div>
                <div class="text-[11px] uppercase tracking-wider text-white/60">Support</div>
            </div>
        </div>
    </div>
</section>

{{-- ================= BRAND MARQUEE ================= --}}
<section class="bg-white border-y border-slate-200 overflow-hidden">
    <div class="max-w-7xl mx-auto py-4">
        <div class="marquee marquee-mask whitespace-nowrap text-slate-400 font-bold uppercase tracking-widest text-sm md:text-base">
            @php $brandsRow = ['Netflix','Spotify','Disney+','YouTube','CapCut','Canva','ChatGPT','Microsoft 365','Adobe','HBO Max','Vidio','Prime Video','Duolingo','Notion']; @endphp
            @foreach (array_merge($brandsRow, $brandsRow) as $b)
                <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-brand"></span>{{ $b }}</span>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= FLASH SALE ================= --}}
@if ($flashsales->isNotEmpty())
    @php $earliestEnd = $flashsales->min('end_at'); @endphp
    <section class="relative py-8 md:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl bg-gradient-to-r from-rose-500 via-orange-500 to-amber-400 p-[2px] shadow-xl shadow-rose-500/20">
                <div class="rounded-[0.95rem] bg-slate-900 text-white px-4 md:px-5 py-4 md:py-5">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div class="flex items-center gap-2.5">
                            <span class="badge-flash w-9 h-9 md:w-10 md:h-10 rounded-lg grid place-items-center text-lg md:text-xl">⚡</span>
                            <div>
                                <h2 class="font-extrabold text-lg md:text-xl tracking-tight leading-none">FLASH SALE</h2>
                                <p class="text-[11px] md:text-xs text-white/55 mt-0.5">Promo terbatas — stok cepat habis!</p>
                            </div>
                        </div>
                        <div id="flashsale-countdown" class="flex items-center gap-1">
                            <span class="hidden sm:inline text-[10px] uppercase tracking-wider text-white/60 mr-1">Berakhir</span>
                            <span id="fs-h" class="countdown-chip text-white font-mono font-bold rounded-md px-2 py-1.5 min-w-[34px] text-center text-sm">00</span>
                            <span class="font-bold text-sm">:</span>
                            <span id="fs-m" class="countdown-chip text-white font-mono font-bold rounded-md px-2 py-1.5 min-w-[34px] text-center text-sm">00</span>
                            <span class="font-bold text-sm">:</span>
                            <span id="fs-s" class="countdown-chip text-white font-mono font-bold rounded-md px-2 py-1.5 min-w-[34px] text-center text-sm">00</span>
                        </div>
                    </div>
                    <div class="flex gap-2.5 md:gap-3 overflow-x-auto pb-1 -mx-1 px-1 snap-x snap-mandatory scrollbar-thin">
                        @foreach ($flashsales as $fs)
                            @php $variant = $fs->variant; $product = $variant?->product; @endphp
                            @if ($variant && $product)
                                <a href="{{ route('checkout.show', [$product, $variant]) }}"
                                   class="group shrink-0 w-[140px] sm:w-[150px] md:w-[160px] snap-start rounded-xl bg-white text-slate-900 overflow-hidden hover:-translate-y-0.5 transition shadow-md shadow-black/30">
                                    <div class="relative aspect-[4/3] bg-gradient-to-br from-slate-50 to-slate-200 overflow-hidden">
                                        @if ($product->imageUrl())
                                            <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="w-full h-full object-cover product-img">
                                        @else
                                            <div class="w-full h-full grid place-items-center text-2xl text-slate-400">🎬</div>
                                        @endif
                                        <span class="absolute top-1.5 left-1.5 badge-flash text-white text-[10px] font-bold rounded px-1.5 py-0.5">-{{ $fs->discountPercent() }}%</span>
                                        <span class="absolute bottom-1.5 right-1.5 inline-flex items-center text-[9px] font-semibold rounded px-1.5 py-0.5 bg-slate-900/85 text-white">Sisa {{ $fs->remaining() }}</span>
                                    </div>
                                    <div class="p-2.5">
                                        <div class="text-[9px] uppercase tracking-wider text-brand font-bold">{{ optional($product->category)->name }}</div>
                                        <div class="font-semibold text-[12px] leading-tight line-clamp-2 mt-0.5">{{ $product->name }}</div>
                                        <div class="text-[10px] text-slate-500 mt-0.5">{{ $variant->name }}</div>
                                        <div class="mt-1.5 flex items-baseline gap-1.5 flex-wrap">
                                            <span class="font-extrabold text-rose-600 text-[13px]">Rp {{ number_format($fs->flash_price, 0, ',', '.') }}</span>
                                            <span class="price-strike text-[10px]">Rp {{ number_format($variant->price, 0, ',', '.') }}</span>
                                        </div>
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

{{-- ================= KATALOG ================= --}}
<section id="katalog" class="py-12 md:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-7">
            <div>
                <span class="inline-block text-[11px] uppercase tracking-wider font-bold text-brand mb-2">Katalog Lengkap</span>
                <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight">Semua Produk Premium</h2>
                <p class="text-slate-500 mt-2">Pilih kategori favoritmu — auto-deliver dalam hitungan detik.</p>
            </div>
        </div>

        @php
            $catIcons = [
                'streaming' => '🎬', 'editing' => '✂️', 'musik' => '🎵', 'music' => '🎵',
                'vpn' => '🛡️', 'productivity' => '⚙️', 'education' => '📚', 'design' => '🎨',
                'ai' => '🤖', 'tools' => '🧰',
            ];
        @endphp
        <div class="flex items-center gap-2 mb-7 overflow-x-auto pb-2 -mx-1 px-1">
            <a href="{{ route('home') }}"
               class="shrink-0 inline-flex items-center gap-1.5 rounded-full text-sm font-semibold px-4 py-2 transition border
                      {{ ! $activeCategory ? 'btn-brand border-transparent shadow-soft' : 'bg-white border-slate-200 hover:border-brand text-slate-700' }}">
                <span>✨</span> Semua
            </a>
            @foreach ($categories as $cat)
                @php $icon = $catIcons[strtolower($cat->slug)] ?? $catIcons[strtolower($cat->name)] ?? '🛒'; @endphp
                <a href="{{ route('home', ['kategori' => $cat->slug]) }}"
                   class="shrink-0 inline-flex items-center gap-1.5 rounded-full text-sm font-semibold px-4 py-2 transition border
                          {{ $activeCategory === $cat->slug ? 'btn-brand border-transparent shadow-soft' : 'bg-white border-slate-200 hover:border-brand text-slate-700' }}">
                    <span>{{ $icon }}</span> {{ $cat->name }}
                </a>
            @endforeach
        </div>

        @if ($products->isEmpty())
            <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-slate-300">
                <div class="text-6xl mb-3">🛒</div>
                <p class="text-slate-500">Belum ada produk yang cocok dengan pencarian/filter kamu.</p>
                <a href="{{ route('home') }}" class="inline-block mt-4 rounded-xl btn-brand font-semibold px-5 py-2.5 text-sm">Lihat Semua Produk</a>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-5">
                @foreach ($products as $product)
                    @php
                        $stockSum = $product->variants->sum(fn ($v) => $v->available_stocks_count ?? 0);
                        $lowest = $product->lowestPrice();
                        $firstVariant = $product->variants->first();
                        $fs = $firstVariant?->activeFlashsale();
                    @endphp
                    <article class="product-card group rounded-2xl bg-white border border-slate-200 overflow-hidden hover:border-brand hover:shadow-soft">
                        <a href="{{ route('products.show', $product) }}" class="block">
                            <div class="relative aspect-[5/4] bg-gradient-to-br from-slate-50 to-slate-200 overflow-hidden">
                                @if ($product->imageUrl())
                                    <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="product-img w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full grid place-items-center text-3xl text-slate-300">🎬</div>
                                @endif
                                <div class="absolute inset-x-0 top-0 p-2 flex items-start justify-between gap-2 pointer-events-none">
                                    <div class="flex flex-col gap-1">
                                        @if ($fs)
                                            <span class="badge-flash text-white text-[10px] font-bold rounded-md px-2 py-1 inline-flex items-center gap-1">⚡ -{{ $fs->discountPercent() }}%</span>
                                        @endif
                                        @if ($product->is_best_seller)
                                            <span class="text-[10px] font-bold rounded-md px-2 py-1 bg-amber-400 text-amber-950 inline-flex items-center gap-1">★ Best</span>
                                        @endif
                                    </div>
                                    <span class="text-[10px] font-semibold rounded-md px-1.5 py-0.5 bg-slate-900/80 text-white">
                                        {{ $stockSum > 0 ? "Stok $stockSum" : 'Habis' }}
                                    </span>
                                </div>
                                <div class="absolute inset-x-0 bottom-0 h-12 bg-gradient-to-t from-black/30 to-transparent pointer-events-none"></div>
                            </div>
                        </a>
                        <div class="p-3.5">
                            <div class="text-[10px] uppercase tracking-wider text-brand font-bold mb-1">{{ optional($product->category)->name }}</div>
                            <a href="{{ route('products.show', $product) }}" class="block">
                                <h3 class="font-bold leading-tight text-sm line-clamp-2 mb-1.5 group-hover:text-brand">{{ $product->name }}</h3>
                            </a>
                            @if ($product->short_description)
                                <p class="text-xs text-slate-500 line-clamp-2 mb-2.5">{{ $product->short_description }}</p>
                            @endif
                            <div class="flex items-baseline gap-1.5 mb-3">
                                @if ($fs)
                                    <span class="text-base font-extrabold text-rose-600">Rp {{ number_format($fs->flash_price, 0, ',', '.') }}</span>
                                    <span class="price-strike">Rp {{ number_format($firstVariant->price, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Mulai</span>
                                    <span class="text-base font-extrabold text-slate-900">Rp {{ number_format($lowest, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('products.show', $product) }}"
                                   class="flex-1 text-center rounded-xl btn-brand font-semibold text-xs py-2.5 hover:shadow-soft">
                                    Beli Sekarang
                                </a>
                                <a href="{{ route('products.show', $product) }}"
                                   class="rounded-xl border border-slate-200 hover:border-brand hover:text-brand text-slate-500 text-xs py-2.5 px-3" aria-label="Detail">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                                </a>
                            </div>
                            @if ($product->sold_count > 0)
                                <div class="mt-2.5 flex items-center gap-1 text-[11px] text-slate-400">
                                    <svg class="w-3.5 h-3.5 text-emerald-500" viewBox="0 0 24 24" fill="currentColor"><path d="M13 9V3L7 12h4v6l6-9h-4z"/></svg>
                                    {{ number_format($product->sold_count, 0, ',', '.') }} terjual
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ================= HOW IT WORKS ================= --}}
<section class="py-14 md:py-20 bg-white border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="inline-block text-[11px] uppercase tracking-wider font-bold text-brand mb-2">Mudah & Cepat</span>
            <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight">Beli Akun Premium dalam 3 Langkah</h2>
            <p class="text-slate-500 mt-3">Tanpa daftar akun, tanpa cart. Pilih, bayar, terima — selesai dalam hitungan menit.</p>
        </div>

        @php
            $steps = [
                ['num'=>'01','icon'=>'🛍️','title'=>'Pilih Produk','desc'=>'Cari & pilih akun premium dari katalog kami.'],
                ['num'=>'02','icon'=>'💳','title'=>'Bayar QRIS / VA','desc'=>'Bayar lewat QRIS, VA, atau E-Wallet — semua otomatis terverifikasi.'],
                ['num'=>'03','icon'=>'📩','title'=>'Akun Diterima','desc'=>'Kredensial muncul di halaman invoice & bisa di-copy langsung.'],
            ];
        @endphp
        <div class="relative grid md:grid-cols-3 gap-5 md:gap-2">
            <div class="hidden md:block absolute top-12 left-[16%] right-[16%] h-0.5 step-line rounded-full"></div>
            @foreach ($steps as $i => $step)
                <div class="relative text-center px-4">
                    <div class="relative inline-flex items-center justify-center w-24 h-24 rounded-full bg-white border-2 border-brand shadow-soft text-4xl mb-4">
                        {{ $step['icon'] }}
                        <span class="absolute -top-1 -right-1 bg-brand text-white text-xs font-bold w-7 h-7 rounded-full grid place-items-center">{{ $step['num'] }}</span>
                    </div>
                    <h3 class="font-bold text-lg mb-1">{{ $step['title'] }}</h3>
                    <p class="text-sm text-slate-500 max-w-xs mx-auto">{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= WHY US (FEATURE GRID) ================= --}}
<section class="py-14 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="inline-block text-[11px] uppercase tracking-wider font-bold text-brand mb-2">Kenapa Pilih Kami</span>
            <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight">Toko Akun Premium yang Beda</h2>
        </div>
        @php
            $features = [
                ['svg'=>'<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2 3 14h7l-1 8 10-12h-7l1-8z"/></svg>','title'=>'Auto Delivery','desc'=>'Akun otomatis dikirim ke invoice setelah pembayaran terverifikasi.'],
                ['svg'=>'<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>','title'=>'Garansi Penuh','desc'=>'Akun bermasalah? Kami ganti tanpa ribet sesuai durasi paket.'],
                ['svg'=>'<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M2 10h20M6 15h2"/></svg>','title'=>'Pembayaran Aman','desc'=>'QRIS, VA, & E-Wallet via Pakasir — terenkripsi & PCI compliant.'],
                ['svg'=>'<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>','title'=>'Live Chat 24/7','desc'=>'Tim support siap bantu via WhatsApp kapan saja.'],
                ['svg'=>'<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>','title'=>'Stok Update Realtime','desc'=>'Stok ditampilkan live — gak perlu DM dulu untuk cek ketersediaan.'],
                ['svg'=>'<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.4 14.5 16 10 4 20"/><path d="M14 7h.01M3 3h18v18H3z"/></svg>','title'=>'Privasi Terjamin','desc'=>'Kredensial akun terenkripsi AES-256 — admin pun tak baca plaintext sembarangan.'],
            ];
        @endphp
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
            @foreach ($features as $f)
                <div class="group rounded-2xl bg-white border border-slate-200 p-6 hover:border-brand hover:shadow-card transition">
                    <div class="w-12 h-12 rounded-xl bg-brand/10 text-brand grid place-items-center mb-3 group-hover:scale-110 transition">
                        {!! $f['svg'] !!}
                    </div>
                    <div class="font-bold text-base">{{ $f['title'] }}</div>
                    <div class="text-sm text-slate-500 mt-1">{{ $f['desc'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= TESTIMONI ================= --}}
@if ($testimonials->isNotEmpty())
<section class="py-14 md:py-20 bg-white border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="inline-block text-[11px] uppercase tracking-wider font-bold text-brand mb-2">Testimoni</span>
            <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight">Apa Kata Pelanggan Kami</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-5">
            @foreach ($testimonials as $t)
                <div class="rounded-2xl bg-gradient-to-br from-slate-50 to-white border border-slate-200 p-6 hover:shadow-card transition">
                    <div class="flex items-center gap-1 mb-3 text-amber-400">
                        @for ($i = 0; $i < $t->rating; $i++)
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.39 4.84L20 8l-4 3.9.94 5.46L12 14.77 7.06 17.36 8 11.9 4 8l5.61-1.16L12 2z"/></svg>
                        @endfor
                        @for ($i = $t->rating; $i < 5; $i++)
                            <svg class="w-4 h-4 text-slate-200" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.39 4.84L20 8l-4 3.9.94 5.46L12 14.77 7.06 17.36 8 11.9 4 8l5.61-1.16L12 2z"/></svg>
                        @endfor
                    </div>
                    <p class="text-slate-700 leading-relaxed text-sm">"{{ $t->content }}"</p>
                    <div class="mt-5 flex items-center gap-3">
                        @if ($t->avatarUrl())
                            <img src="{{ $t->avatarUrl() }}" alt="{{ $t->name }}"
                                 class="w-10 h-10 rounded-full object-cover ring-2 ring-brand/20"
                                 loading="lazy">
                        @else
                            <div class="w-10 h-10 rounded-full bg-brand/15 text-brand grid place-items-center font-bold">{{ $t->initials() }}</div>
                        @endif
                        <div>
                            <div class="font-semibold text-sm">{{ $t->name }}</div>
                            @if ($t->role)
                                <div class="text-xs text-slate-500">{{ $t->role }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ================= CTA BANNER ================= --}}
<section class="py-14 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl gradient-hero text-white p-8 md:p-12">
            <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full blob bg-fuchsia-500/30 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-12 w-80 h-80 rounded-full blob delay-1 bg-cyan-400/20 blur-3xl"></div>
            <div class="relative grid md:grid-cols-2 gap-6 items-center">
                <div>
                    <h2 class="text-3xl md:text-4xl font-extrabold leading-tight">Belum Nemu Akun yang Kamu Cari?</h2>
                    <p class="text-white/80 mt-3 max-w-lg">Chat admin via WhatsApp — kami bantu carikan akun premium request khusus, harga tetap bersahabat.</p>
                </div>
                <div class="flex flex-col sm:flex-row md:justify-end gap-3">
                    @if (! empty($site?->wa_number))
                        <a href="{{ $site?->waLink() }}" target="_blank" rel="noopener"
                           class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-semibold px-5 py-3 shadow-lg shadow-emerald-500/30 transition">
                            <svg viewBox="0 0 24 24" class="w-5 h-5" fill="currentColor"><path d="M17.6 14c-.3-.1-1.7-.8-1.9-.9-.3-.1-.5-.1-.7.2-.2.3-.8.9-.9 1.1-.2.2-.3.2-.6.1-1.6-.8-2.7-1.4-3.7-3.2-.3-.5.3-.5.8-1.5.1-.2 0-.4 0-.5-.1-.1-.7-1.6-.9-2.2-.2-.6-.5-.5-.7-.5-.2 0-.4 0-.6 0s-.5.1-.8.4c-.3.3-1.1 1-1.1 2.4s1.1 2.8 1.3 3c.2.2 2.2 3.5 5.4 4.8 2.6 1 3.1.8 3.7.8.6 0 1.7-.7 2-1.4.2-.7.2-1.2.2-1.4 0-.1-.3-.2-.6-.3z"/><path d="M12 0C5.4 0 0 5.4 0 12c0 2.1.5 4.1 1.6 5.9L0 24l6.3-1.7c1.7.9 3.7 1.4 5.7 1.4 6.6 0 12-5.4 12-12S18.6 0 12 0zm0 21.8c-1.8 0-3.6-.5-5.1-1.4l-.4-.2-3.7 1 1-3.6-.2-.4c-1-1.6-1.5-3.4-1.5-5.2C2.1 6.5 6.5 2.1 12 2.1S21.9 6.5 21.9 12 17.5 21.8 12 21.8z"/></svg>
                            Chat Admin di WhatsApp
                        </a>
                    @endif
                    <a href="{{ route('home') }}#katalog"
                       class="inline-flex items-center justify-center gap-2 rounded-xl glass hover:bg-white/20 font-semibold px-5 py-3 transition">
                        Jelajahi Katalog
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= ARTIKEL ================= --}}
@if ($articles->isNotEmpty())
    <section class="pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-7">
                <div>
                    <span class="inline-block text-[11px] uppercase tracking-wider font-bold text-brand mb-2">Blog</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight">Artikel & Tips Terbaru</h2>
                    <p class="text-slate-500 mt-2">Update info terbaru seputar akun premium.</p>
                </div>
                <a href="{{ route('articles.index') }}" class="text-sm font-semibold text-brand hover:underline shrink-0">Lihat Semua →</a>
            </div>
            <div class="grid md:grid-cols-3 gap-5">
                @foreach ($articles as $article)
                    <a href="{{ route('articles.show', $article) }}" class="block rounded-2xl bg-white border border-slate-200 overflow-hidden hover:shadow-soft hover:border-brand transition group">
                        <div class="aspect-video bg-gradient-to-br from-slate-100 to-slate-200 overflow-hidden">
                            @if ($article->cover_image)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($article->cover_image) }}" alt="{{ $article->title }}" class="product-img w-full h-full object-cover">
                            @else
                                <div class="w-full h-full grid place-items-center text-4xl text-slate-400">📝</div>
                            @endif
                        </div>
                        <div class="p-5">
                            <h3 class="font-bold leading-snug line-clamp-2 group-hover:text-brand mb-2">{{ $article->title }}</h3>
                            @if ($article->excerpt)
                                <p class="text-sm text-slate-500 line-clamp-3">{{ $article->excerpt }}</p>
                            @endif
                            @if ($article->published_at)
                                <p class="text-xs text-slate-400 mt-3 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                    {{ $article->published_at->translatedFormat('d M Y') }}
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif

@endsection
