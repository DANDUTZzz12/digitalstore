@extends('layouts.app')
@section('title', $article->title . ' — ' . ($site->store_name ?? 'Akhpremium Store'))
@section('meta_description', $article->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($article->content), 150))

@section('content')
<article class="py-12 md:py-16">
    <div class="max-w-3xl mx-auto px-4">
        <a href="{{ route('articles.index') }}" class="text-sm text-brand font-semibold hover:underline">← Kembali ke Artikel</a>
        <h1 class="mt-3 text-3xl md:text-4xl font-extrabold tracking-tight">{{ $article->title }}</h1>
        <div class="mt-2 text-sm text-slate-500">
            @if ($article->published_at)
                {{ $article->published_at->translatedFormat('d F Y') }}
            @endif
        </div>
        @if ($article->cover_image)
            <div class="mt-6 rounded-2xl overflow-hidden border border-slate-200">
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($article->cover_image) }}" alt="" class="w-full">
            </div>
        @endif
        <div class="mt-8 prose-content text-slate-700">
            {!! $article->content !!}
        </div>
    </div>

    @if ($related->isNotEmpty())
        <div class="max-w-7xl mx-auto px-4 mt-16">
            <h2 class="text-xl font-bold mb-4">Artikel Lainnya</h2>
            <div class="grid md:grid-cols-3 gap-4">
                @foreach ($related as $r)
                    <a href="{{ route('articles.show', $r) }}" class="block rounded-2xl bg-white border border-slate-200 overflow-hidden hover:shadow-soft transition group">
                        <div class="aspect-video bg-slate-100">
                            @if ($r->cover_image)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($r->cover_image) }}" alt="" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-3xl text-slate-400">📝</div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold line-clamp-2 group-hover:text-brand">{{ $r->title }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</article>
@endsection
