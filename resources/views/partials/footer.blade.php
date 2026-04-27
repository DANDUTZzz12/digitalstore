<footer class="mt-20 bg-slate-900 text-slate-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid gap-10 md:grid-cols-4">
        <div class="md:col-span-2">
            <div class="flex items-center gap-2 mb-3">
                @if (! empty($site?->logo_path))
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($site->logo_path) }}"
                         alt="{{ $site->store_name }}" class="w-8 h-8 rounded-md object-cover">
                @else
                    <span class="w-8 h-8 rounded-md flex items-center justify-center text-white font-bold text-sm" style="background: var(--brand);">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($site->store_name ?? 'A', 0, 1)) }}</span>
                @endif
                <span class="font-extrabold text-white text-lg">{{ $site->store_name ?? 'AKHPREMIUM' }}</span>
            </div>
            <p class="text-sm leading-relaxed max-w-md">
                {{ $site->footer_about ?? 'Penyedia akun premium legal & resmi dengan sistem auto-delivery 24 jam.' }}
            </p>
            <div class="mt-4 flex items-center gap-3">
                @if ($site?->wa_number)
                    <a href="{{ $site->waLink() }}" target="_blank" rel="noopener" title="WhatsApp"
                       class="w-9 h-9 rounded-full bg-emerald-500 hover:bg-emerald-600 inline-flex items-center justify-center text-white">
                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor"><path d="M19.07 4.93A10 10 0 0 0 4.13 18.4L3 22l3.72-1.1A10 10 0 1 0 19.07 4.93Z"/></svg>
                    </a>
                @endif
                @if ($site?->instagram_url)
                    <a href="{{ $site->instagram_url }}" target="_blank" rel="noopener" title="Instagram"
                       class="w-9 h-9 rounded-full bg-pink-500 hover:bg-pink-600 inline-flex items-center justify-center text-white">
                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor"><path d="M7 2C4.24 2 2 4.24 2 7v10c0 2.76 2.24 5 5 5h10c2.76 0 5-2.24 5-5V7c0-2.76-2.24-5-5-5H7zm0 2h10a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3H7a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3zm10.5 1.75a1 1 0 1 0 0 2 1 1 0 0 0 0-2zM12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 2a3 3 0 1 1 0 6 3 3 0 0 1 0-6z"/></svg>
                    </a>
                @endif
                @if ($site?->tiktok_url)
                    <a href="{{ $site->tiktok_url }}" target="_blank" rel="noopener" title="TikTok"
                       class="w-9 h-9 rounded-full bg-slate-800 hover:bg-slate-700 inline-flex items-center justify-center text-white">
                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor"><path d="M16 3a5 5 0 0 0 5 5v3a8 8 0 0 1-5-1.74V16a6 6 0 1 1-6-6c.34 0 .68.03 1 .09v3.16A3 3 0 1 0 13 16V3h3z"/></svg>
                    </a>
                @endif
                @if ($site?->telegram_url)
                    <a href="{{ $site->telegram_url }}" target="_blank" rel="noopener" title="Telegram"
                       class="w-9 h-9 rounded-full bg-sky-500 hover:bg-sky-600 inline-flex items-center justify-center text-white">
                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor"><path d="M9.7 14.3 9.4 19c.5 0 .7-.2.9-.5l2.2-2.1 4.5 3.3c.8.5 1.4.2 1.6-.8L21.9 4c.3-1.2-.5-1.7-1.3-1.4L2.6 9.6c-1.2.5-1.2 1.2-.2 1.5l4.6 1.4 10.7-6.7c.5-.3 1-.1.6.2L9.7 14.3z"/></svg>
                    </a>
                @endif
                @if ($site?->facebook_url)
                    <a href="{{ $site->facebook_url }}" target="_blank" rel="noopener" title="Facebook"
                       class="w-9 h-9 rounded-full bg-blue-600 hover:bg-blue-700 inline-flex items-center justify-center text-white">
                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor"><path d="M13 22v-8h3l.5-4H13V7.5c0-1.1.3-1.9 1.9-1.9H17V2.1c-.3 0-1.5-.1-2.8-.1-2.8 0-4.7 1.7-4.7 4.8V10H7v4h2.5v8H13z"/></svg>
                    </a>
                @endif
            </div>
        </div>

        <div>
            <h4 class="text-white font-semibold mb-3">Pusat Bantuan</h4>
            <ul class="space-y-2 text-sm">
                <li><a class="hover:text-white" href="{{ route('pages.faq') }}">FAQ</a></li>
                <li><a class="hover:text-white" href="{{ route('pages.how-to-order') }}">Cara Pemesanan</a></li>
                <li><a class="hover:text-white" href="{{ route('pages.terms') }}">Ketentuan Order</a></li>
                <li><a class="hover:text-white" href="{{ route('pages.cek-invoice') }}">Cek Invoice</a></li>
                <li><a class="hover:text-white" href="{{ route('articles.index') }}">Artikel & Tips</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-white font-semibold mb-3">Kontak</h4>
            <ul class="space-y-2 text-sm">
                @if ($site?->contact_email)
                    <li>Email: <a class="hover:text-white" href="mailto:{{ $site->contact_email }}">{{ $site->contact_email }}</a></li>
                @endif
                @if ($site?->wa_number)
                    <li>WhatsApp: <a class="hover:text-white" href="{{ $site->waLink() }}">+{{ $site->wa_number }}</a></li>
                @endif
                @if ($site?->whatsapp_channel_url)
                    <li><a class="hover:text-white" href="{{ $site->whatsapp_channel_url }}">Gabung Channel WhatsApp</a></li>
                @endif
                @if ($site?->support_hours)
                    <li class="text-slate-400">Jam Support: {{ $site->support_hours }}</li>
                @endif
            </ul>
            <div class="mt-3 inline-flex items-center gap-2 text-xs px-3 py-1.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span></span>
                Sistem Otomatis 24/7
            </div>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-2 text-xs text-slate-400">
            <div>© {{ now()->year }} {{ $site->store_name ?? 'AKHPREMIUM STORE' }}. Powered by Laravel + Pakasir.</div>
            <div class="flex items-center gap-4">
                <a href="{{ route('pages.terms') }}" class="hover:text-white">Terms</a>
                <span class="opacity-30">·</span>
                <a href="{{ route('pages.faq') }}" class="hover:text-white">FAQ</a>
            </div>
        </div>
    </div>
</footer>
