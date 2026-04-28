@extends('layouts.app')

@section('title', 'Dashboard Akun')

@section('content')
@section('account_content')
    <div class="rounded-2xl bg-gradient-to-br from-slate-900 to-indigo-900 text-white p-6 md:p-8 mb-6 shadow-card">
        <p class="text-sm text-white/60">Halo,</p>
        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight mt-1">{{ $user->name }} 👋</h1>
        <p class="text-sm text-white/70 mt-1">Cek history pesanan kamu, kelola profil, atau lanjut belanja akun premium baru.</p>
        <div class="mt-5 flex flex-wrap gap-2">
            <a href="{{ route('home') }}" class="rounded-full bg-white text-slate-900 font-semibold text-sm px-4 py-2 hover:bg-white/90">Belanja Lagi</a>
            <a href="{{ route('account.orders.index') }}" class="rounded-full bg-white/10 backdrop-blur text-white font-semibold text-sm px-4 py-2 hover:bg-white/20">Lihat Semua Pesanan</a>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="rounded-2xl bg-white border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Total Order</p>
            <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Berhasil</p>
            <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $stats['paid'] }}</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Pending</p>
            <p class="text-2xl font-extrabold text-amber-600 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Total Belanja</p>
            <p class="text-lg md:text-xl font-extrabold text-slate-900 mt-1">Rp {{ number_format($stats['total_spend'], 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-bold">Pesanan Terbaru</h2>
            <a href="{{ route('account.orders.index') }}" class="text-sm text-brand font-semibold hover:underline">Lihat semua →</a>
        </div>

        @include('account._orders-table', ['orders' => $orders, 'paginated' => false])
    </div>
@endsection

@include('account._layout')
@endsection
