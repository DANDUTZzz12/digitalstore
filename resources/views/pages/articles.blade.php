@extends('layouts.app')
@section('title', 'Artikel & Tips — ' . ($site->store_name ?? 'Akhpremium Store'))

@section('content')
<section class="py-12 md:py-16">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-3xl md:text-4xl font-extrabold text-center">Artikel & Tips</h1>
        <p class="text-slate-500 text-center mt-2">Update info terbaru, tips, dan tutorial seputar akun premium.</p>

        @if ($articles->isEmpty())
            <div class="mt-10 rounded-2xl bg-white border border-dashed border-slate-300 p-10 text-center text-slate-500">
                Belum ada artikel.
            </div>
        @else
            <div class="mt-10 grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($articles as $article)
                    <a href="{{ route('articles.show', $article) }}" class="block rounded-2xl bg-white border border-slate-200 overflow-hidden hover:shadow-soft transition group">
                        <div class="aspect-video bg-gradient-to-br from-slate-100 to-slate-200">
                            @if ($article->cover_image)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($article->cover_image) }}" alt="" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-4xl text-slate-400">📝</div>
                            @endif
                        </div>
                        <div class="p-5">
                            <h3 class="font-bold leading-snug line-clamp-2 group-hover:text-brand">{{ $article->title }}</h3>
                            @if ($article->excerpt)
                                <p class="text-sm text-slate-500 mt-2 line-clamp-3">{{ $article->excerpt }}</p>
                            @endif
                            @if ($article->published_at)
                                <p class="text-xs text-slate-400 mt-3">{{ $article->published_at->translatedFormat('d M Y') }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-8">{{ $articles->links() }}</div>
        @endif
    </div>
</section>
@endsection
