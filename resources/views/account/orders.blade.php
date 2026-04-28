@extends('layouts.app')

@section('title', 'History Pesanan')

@section('content')
@section('account_content')
    <h1 class="text-2xl font-extrabold tracking-tight">History Pesanan</h1>
    <p class="text-sm text-slate-500 mt-1">Semua pesanan kamu — guest order dengan email yang sama otomatis tergabung.</p>

    @php
        $tabs = [
            ['', 'Semua'],
            ['paid', 'Lunas'],
            ['pending', 'Menunggu'],
            ['cancelled', 'Dibatalkan'],
            ['expired', 'Kadaluarsa'],
        ];
    @endphp
    <div class="mt-4 flex flex-wrap gap-1.5">
        @foreach ($tabs as [$st, $label])
            <a href="{{ $st ? route('account.orders.index', ['status' => $st]) : route('account.orders.index') }}"
               class="rounded-full text-xs font-semibold px-3 py-1.5 border transition
                      {{ ($activeStatus ?? '') === $st ? 'bg-brand text-white border-brand' : 'border-slate-200 text-slate-600 hover:border-brand hover:text-brand' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="mt-5 rounded-2xl bg-white border border-slate-200 overflow-hidden">
        @include('account._orders-table', ['orders' => $orders, 'paginated' => true])
    </div>
@endsection

@include('account._layout')
@endsection
