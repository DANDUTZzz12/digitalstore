@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<section class="py-10 md:py-14">
    <div class="max-w-md mx-auto px-4">
        <div class="rounded-2xl bg-white border border-slate-200 shadow-card p-6 md:p-8">
            <h1 class="text-2xl font-extrabold tracking-tight">Masuk</h1>
            <p class="text-sm text-slate-500 mt-1">Lihat history pesanan & checkout lebih cepat.</p>

            <form method="POST" action="{{ route('login.attempt') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="email">Email</label>
                    <input type="email" id="email" name="email" required autofocus value="{{ old('email') }}"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand"
                           placeholder="kamu@email.com">
                    @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="password">Password</label>
                    <input type="password" id="password" name="password" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand">
                    @error('password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-center justify-between text-sm">
                    <label class="inline-flex items-center gap-2 text-slate-600">
                        <input type="checkbox" name="remember" value="1" class="rounded border-slate-300">
                        Ingat saya
                    </label>
                    <a href="{{ route('password.request') }}" class="text-brand font-semibold hover:underline">Lupa password?</a>
                </div>
                <button type="submit" class="w-full rounded-xl btn-brand font-semibold py-2.5">Masuk</button>
            </form>

            <p class="mt-5 text-center text-sm text-slate-500">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-brand font-semibold hover:underline">Daftar di sini</a>
            </p>
        </div>
    </div>
</section>
@endsection
