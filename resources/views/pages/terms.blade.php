@extends('layouts.app')
@section('title', 'Ketentuan Order — ' . ($site?->store_name ?? 'Akhpremium Store'))

@section('content')
<section class="py-12 md:py-16">
    <div class="max-w-3xl mx-auto px-4">
        <h1 class="text-3xl md:text-4xl font-extrabold text-center">Ketentuan Order</h1>
        <p class="text-slate-500 text-center mt-2">Mohon baca ketentuan berikut sebelum melakukan pemesanan.</p>

        <div class="mt-8 rounded-2xl bg-white border border-slate-200 p-6 md:p-8 prose-content text-slate-700">
            @if (! empty($site?->terms_html))
                {!! $site?->terms_html !!}
            @else
                <h2>1. Garansi Akun</h2>
                <p>Setiap akun yang kami jual memiliki garansi sesuai durasi pembelian. Apabila akun bermasalah karena kesalahan dari pihak kami, akan kami ganti tanpa biaya tambahan.</p>

                <h2>2. Penggunaan Akun</h2>
                <ul>
                    <li>Dilarang mengubah email & password pada akun sharing.</li>
                    <li>Dilarang menjual ulang akun ke pihak lain.</li>
                    <li>Dilarang melakukan tindakan yang melanggar Terms of Service platform terkait.</li>
                </ul>

                <h2>3. Refund</h2>
                <p>Refund hanya berlaku jika akun yang dikirim tidak dapat digunakan dan tidak bisa kami ganti dengan akun pengganti.</p>

                <h2>4. Privasi</h2>
                <p>Data pembeli (email & WhatsApp) hanya digunakan untuk keperluan pengiriman akun & komunikasi dukungan. Kami tidak membagikan data ke pihak ketiga.</p>

                <h2>5. Komplain</h2>
                <p>Komplain dapat diajukan melalui WhatsApp resmi atau email yang tertera pada bagian footer website.</p>
            @endif
        </div>
    </div>
</section>
@endsection
