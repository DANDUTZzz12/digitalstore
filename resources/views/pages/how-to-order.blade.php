@extends('layouts.app')
@section('title', 'Cara Pemesanan — ' . ($site->store_name ?? 'Akhpremium Store'))

@section('content')
<section class="py-12 md:py-16">
    <div class="max-w-4xl mx-auto px-4">
        <h1 class="text-3xl md:text-4xl font-extrabold text-center">Cara Pemesanan</h1>
        <p class="text-slate-500 text-center mt-2">Pesan akun premium di {{ $site->store_name ?? 'toko' }} hanya butuh 4 langkah singkat.</p>

        <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $steps = [
                    ['1','🛒','Pilih Produk','Pilih kategori produk dan paket durasi yang kamu inginkan.'],
                    ['2','📝','Isi Email','Masukkan email valid — kredensial akan dikirim ke email tersebut.'],
                    ['3','💳','Bayar','Bayar via QRIS, VA, atau E-Wallet melalui Pakasir yang aman.'],
                    ['4','⚡','Akun Diterima','Akun otomatis muncul di halaman invoice setelah pembayaran berhasil.'],
                ];
            @endphp
            @foreach ($steps as [$n,$icon,$title,$desc])
                <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-card">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full btn-brand flex items-center justify-center font-extrabold">{{ $n }}</div>
                        <div class="text-2xl">{{ $icon }}</div>
                    </div>
                    <h3 class="font-bold mt-3">{{ $title }}</h3>
                    <p class="text-sm text-slate-500 mt-1">{{ $desc }}</p>
                </div>
            @endforeach
        </div>

        @if (! empty($site?->how_to_order_html))
            <div class="mt-10 rounded-2xl bg-white border border-slate-200 p-6 prose-content text-sm text-slate-700">
                {!! $site->how_to_order_html !!}
            </div>
        @endif

        <div class="mt-10 text-center">
            <a href="{{ route('home') }}" class="inline-flex rounded-xl btn-brand font-bold px-6 py-3">Mulai Belanja</a>
        </div>
    </div>
</section>
@endsection
