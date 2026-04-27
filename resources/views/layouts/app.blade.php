<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Akhpremium Store'))</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
    </style>
    @stack('head')
</head>
<body class="antialiased pb-20">
    <nav class="bg-white border-b border-purple-100 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="text-2xl font-extrabold bg-gradient-to-r from-purple-600 to-blue-500 text-transparent bg-clip-text tracking-tight">
                        AKHPREMIUM
                    </span>
                </a>
                <div class="flex gap-6 items-center">
                    <a href="{{ route('home') }}" class="text-slate-600 hover:text-purple-600 font-bold transition">Home</a>
                    <a href="{{ route('home') }}#katalog" class="text-slate-600 hover:text-purple-600 font-bold transition">Katalog</a>
                </div>
            </div>
        </div>
    </nav>

    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if (session('warning'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 text-sm">
                {{ session('warning') }}
            </div>
        </div>
    @endif

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        @yield('content')
    </main>

    <footer class="mt-16 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center text-sm text-slate-500">
            &copy; {{ date('Y') }} AKHPREMIUM STORE &middot; Powered by Laravel + Pakasir
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
