@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<section class="py-10 md:py-14">
    <div class="max-w-md mx-auto px-4">
        <div class="rounded-2xl bg-white border border-slate-200 shadow-card p-6 md:p-8">
            <h1 class="text-2xl font-extrabold tracking-tight">Reset Password</h1>
            <p class="text-sm text-slate-500 mt-1">Masukkan password baru kamu.</p>

            <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="email">Email</label>
                    <input type="email" id="email" name="email" required value="{{ old('email', $email) }}"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand">
                    @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="password">Password Baru</label>
                    <input type="password" id="password" name="password" required minlength="8"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand">
                    @error('password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="password_confirmation">Konfirmasi</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand">
                </div>
                <button type="submit" class="w-full rounded-xl btn-brand font-semibold py-2.5">Simpan Password Baru</button>
            </form>
        </div>
    </div>
</section>
@endsection
