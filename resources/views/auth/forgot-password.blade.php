@extends('layouts.app')

@section('title', 'Lupa Password')

@section('content')
<section class="py-10 md:py-14">
    <div class="max-w-md mx-auto px-4">
        <div class="rounded-2xl bg-white border border-slate-200 shadow-card p-6 md:p-8">
            <h1 class="text-2xl font-extrabold tracking-tight">Lupa Password?</h1>
            <p class="text-sm text-slate-500 mt-1">Masukkan email — kami kirim link reset password ke inbox.</p>

            <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="email">Email</label>
                    <input type="email" id="email" name="email" required autofocus value="{{ old('email') }}"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand">
                    @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="w-full rounded-xl btn-brand font-semibold py-2.5">Kirim Link Reset</button>
            </form>

            <p class="mt-5 text-center text-sm text-slate-500">
                <a href="{{ route('login') }}" class="text-brand font-semibold hover:underline">← Kembali ke Login</a>
            </p>
        </div>
    </div>
</section>
@endsection
