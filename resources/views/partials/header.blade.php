<header x-data="{ open: false }" class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center gap-4">
        <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
            @if (! empty($site?->logo_path))
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($site->logo_path) }}"
                     alt="{{ $site->store_name }}" class="w-9 h-9 rounded-lg object-cover">
            @else
                <span class="w-9 h-9 rounded-lg flex items-center justify-center text-white font-bold"
                      style="background: var(--brand);">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($site->store_name ?? 'A', 0, 1)) }}</span>
            @endif
            <span class="font-extrabold tracking-tight text-lg text-slate-900">{{ $site->store_name ?? 'AKHPREMIUM' }}</span>
        </a>

        <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-700 ml-4">
            <a href="{{ route('home') }}" class="hover:text-brand">Produk</a>
            <a href="{{ route('pages.cek-invoice') }}" class="hover:text-brand">Cek Invoice</a>
            <a href="{{ route('articles.index') }}" class="hover:text-brand">Artikel</a>
            <a href="{{ route('pages.faq') }}" class="hover:text-brand">FAQ</a>
            <a href="{{ route('pages.how-to-order') }}" class="hover:text-brand">Cara Pemesanan</a>
            <a href="{{ route('pages.terms') }}" class="hover:text-brand">Ketentuan</a>
        </nav>

        <div class="flex-1"></div>

        @if (! empty($site?->wa_number))
            <a href="{{ $site->waLink() }}" target="_blank" rel="noopener"
               class="hidden md:inline-flex items-center gap-2 rounded-full text-sm font-semibold px-4 py-2 btn-brand transition">
                <svg viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor" aria-hidden="true"><path d="M19.07 4.93A10 10 0 0 0 4.13 18.4L3 22l3.72-1.1A10 10 0 1 0 19.07 4.93Z"/></svg>
                Chat Admin
            </a>
        @endif

        <button type="button" onclick="document.getElementById('mob-nav').classList.toggle('hidden')"
                class="md:hidden p-2 rounded-lg border border-slate-200 text-slate-700">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
        </button>
    </div>

    <div id="mob-nav" class="hidden md:hidden border-t border-slate-200 bg-white">
        <div class="px-4 py-3 flex flex-col gap-2 text-sm font-medium text-slate-700">
            <a href="{{ route('home') }}" class="py-2">Produk</a>
            <a href="{{ route('pages.cek-invoice') }}" class="py-2">Cek Invoice</a>
            <a href="{{ route('articles.index') }}" class="py-2">Artikel</a>
            <a href="{{ route('pages.faq') }}" class="py-2">FAQ</a>
            <a href="{{ route('pages.how-to-order') }}" class="py-2">Cara Pemesanan</a>
            <a href="{{ route('pages.terms') }}" class="py-2">Ketentuan Order</a>
            @if (! empty($site?->wa_number))
                <a href="{{ $site->waLink() }}" target="_blank" rel="noopener" class="py-2 mt-1 inline-flex items-center justify-center rounded-lg btn-brand font-semibold">Chat Admin</a>
            @endif
        </div>
    </div>
</header>
