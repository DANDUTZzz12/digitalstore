@extends('layouts.app')
@section('title', 'FAQ — ' . ($site?->store_name ?? 'Akhpremium Store'))

@section('content')
<section class="py-12 md:py-16">
    <div class="max-w-3xl mx-auto px-4">
        <h1 class="text-3xl md:text-4xl font-extrabold text-center">Frequently Asked Questions</h1>
        <p class="text-slate-500 text-center mt-2">Pertanyaan yang sering ditanyakan seputar produk & layanan kami.</p>

        <div class="mt-8 space-y-3">
            @forelse ($faqs as $faq)
                <details class="rounded-2xl bg-white border border-slate-200 p-5 group open:shadow-card transition">
                    <summary class="flex items-center justify-between gap-4">
                        <h3 class="font-bold text-slate-900">{{ $faq->question }}</h3>
                        <span class="text-2xl text-brand transition group-open:rotate-45 leading-none">+</span>
                    </summary>
                    <div class="mt-3 text-slate-600 text-sm prose-content">{!! nl2br(e($faq->answer)) !!}</div>
                </details>
            @empty
                <div class="rounded-2xl bg-white border border-dashed border-slate-300 p-8 text-center text-slate-500">
                    Belum ada FAQ. Admin bisa menambahkan FAQ di panel admin.
                </div>
            @endforelse
        </div>

        @if (! empty($site?->wa_number))
            <div class="mt-10 rounded-2xl bg-white border border-slate-200 p-6 text-center shadow-card">
                <div class="font-bold">Masih ada pertanyaan?</div>
                <p class="text-slate-500 text-sm mt-1">Hubungi admin via WhatsApp untuk respons cepat.</p>
                <a href="{{ $site?->waLink('Halo admin, saya ingin bertanya:') }}" target="_blank" rel="noopener"
                   class="mt-4 inline-flex rounded-xl btn-brand font-bold text-sm px-5 py-2.5">Chat Admin</a>
            </div>
        @endif
    </div>
</section>
@endsection
