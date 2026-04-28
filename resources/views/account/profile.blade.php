@extends('layouts.app')

@section('title', 'Profil & Password')

@section('content')
@section('account_content')
    <h1 class="text-2xl font-extrabold tracking-tight">Profil & Password</h1>
    <p class="text-sm text-slate-500 mt-1">Atur info akun & ganti password kamu.</p>

    <form method="POST" action="{{ route('account.profile.update') }}" class="mt-6 space-y-5">
        @csrf

        <div class="rounded-2xl bg-white border border-slate-200 p-5 md:p-6 space-y-4">
            <h2 class="font-bold">Informasi Akun</h2>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" required value="{{ old('name', $user->name) }}"
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand">
                @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                <input type="email" disabled value="{{ $user->email }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-500">
                <p class="mt-1 text-[11px] text-slate-400">Email tidak bisa diubah. Hubungi admin via WhatsApp jika perlu.</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="phone">Nomor WhatsApp</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand"
                       placeholder="08xxxxxxxxxx">
                @error('phone')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 p-5 md:p-6 space-y-4">
            <h2 class="font-bold">Ganti Password <span class="text-xs font-normal text-slate-400">(opsional)</span></h2>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="current_password">Password Lama</label>
                <input type="password" id="current_password" name="current_password"
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand">
                @error('current_password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="password">Password Baru</label>
                <input type="password" id="password" name="password" minlength="8"
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand">
                @error('password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="password_confirmation">Konfirmasi Password Baru</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand">
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="rounded-xl btn-brand font-semibold px-6 py-2.5">Simpan Perubahan</button>
        </div>
    </form>
@endsection

@include('account._layout')
@endsection
