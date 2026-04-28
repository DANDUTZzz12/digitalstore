<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Flashsale;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SiteSetting;
use App\Models\Stock;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ---- Admin user default ----
        // is_admin di-set via forceFill karena sengaja TIDAK ada di $fillable
        // (cegah mass-assignment privilege escalation).
        $admin = User::firstOrNew(['email' => 'admin@akhpremium.test']);
        $admin->fill([
            'name' => 'Admin',
            'password' => Hash::make('password'),
        ]);
        $admin->forceFill(['is_admin' => true]);
        $admin->save();

        // ---- Site Settings (singleton) ----
        SiteSetting::updateOrCreate(
            ['id' => 1],
            [
                'store_name' => 'AKHPREMIUM',
                'tagline' => 'Akun Premium Legal & Murah',
                'brand_color' => '#7c3aed',
                'accent_color' => '#06b6d4',
                'hero_title' => 'Akun Premium Legal, Harga Ramah.',
                'hero_subtitle' => 'Pilih produk, bayar QRIS / VA / E-Wallet, akun langsung dikirim ke email kamu. Tanpa cart, tanpa ribet.',
                'contact_email' => 'support@akhpremium.test',
                'wa_number' => '6281234567890',
                'wa_default_message' => 'Halo admin AKHPREMIUM, saya butuh bantuan tentang...',
                'instagram_url' => 'https://instagram.com/akhpremium',
                'tiktok_url' => 'https://tiktok.com/@akhpremium',
                'telegram_url' => 'https://t.me/akhpremium',
                'support_hours' => 'Setiap hari, 09.00 – 22.00 WIB',
                'footer_about' => 'Penyedia akun premium legal & resmi dengan sistem auto-delivery 24 jam.',
            ]
        );

        // ---- Kategori + produk + varian + stok ----
        $streaming = Category::updateOrCreate(['slug' => 'streaming'], ['name' => 'Streaming']);
        $editing = Category::updateOrCreate(['slug' => 'editing'], ['name' => 'Editing']);
        $musik = Category::updateOrCreate(['slug' => 'musik'], ['name' => 'Musik']);

        $netflix = Product::updateOrCreate(
            ['name' => 'Netflix Premium Sharing'],
            [
                'description' => "Akun Netflix Sharing 4K UHD.\nGaransi full durasi. No share password.",
                'short_description' => 'Streaming 4K UHD, garansi full durasi.',
                'terms_html' => '<ul><li>Akun <strong>sharing</strong> — jangan ganti email, password, atau profil utama.</li><li>Garansi <strong>full durasi</strong> selama akun digunakan sesuai aturan.</li><li>Maks. <strong>1 device</strong> aktif per profil.</li><li>Klaim garansi via WhatsApp dengan menyertakan <em>order code</em>.</li></ul>',
                'price' => 25000,
                'is_auto_send' => true,
                'is_best_seller' => true,
                'category_id' => $streaming->id,
            ]
        );

        $capcut = Product::updateOrCreate(
            ['name' => 'CapCut Pro Private'],
            [
                'description' => "CapCut Pro Private — 1 akun 1 user.\nBebas watermark, semua fitur pro aktif.",
                'short_description' => 'Bebas watermark, semua fitur PRO aktif.',
                'terms_html' => '<ul><li>Akun <strong>private</strong> — hanya untuk 1 user.</li><li>Garansi penuh selama durasi paket.</li><li>Tidak diperkenankan login di lebih dari 1 device aktif bersamaan.</li><li>Login pertama wajib lewat panduan yang diberikan di invoice.</li></ul>',
                'price' => 15000,
                'is_auto_send' => true,
                'category_id' => $editing->id,
            ]
        );

        $spotify = Product::updateOrCreate(
            ['name' => 'Spotify Premium Family'],
            [
                'description' => 'Spotify Premium slot Family, bebas iklan dan bisa offline.',
                'short_description' => 'Slot Family, bebas iklan, bisa offline.',
                'terms_html' => '<ul><li>Slot <strong>Family</strong> — wajib set lokasi sesuai instruksi di invoice.</li><li>Bebas iklan & bisa offline.</li><li>Garansi full durasi paket.</li><li>Dilarang ganti email/password slot, jika diganti garansi hangus.</li></ul>',
                'price' => 10000,
                'is_auto_send' => true,
                'is_best_seller' => true,
                'category_id' => $musik->id,
            ]
        );

        $variants = [
            [$netflix, '1 Bulan', 25000, 3],
            [$netflix, '3 Bulan', 65000, 1],
            [$capcut, '1 Bulan', 15000, 2],
            [$spotify, '1 Bulan', 10000, 0],
        ];

        $variantRefs = [];
        foreach ($variants as [$product, $name, $price, $stockCount]) {
            $variant = ProductVariant::updateOrCreate(
                ['product_id' => $product->id, 'name' => $name],
                ['price' => $price]
            );
            $variantRefs[$product->name.'|'.$name] = $variant;

            $existing = $variant->stocks()->count();
            for ($i = $existing; $i < $stockCount; $i++) {
                Stock::create([
                    'product_variant_id' => $variant->id,
                    'email_or_phone' => 'demo-'.strtolower($product->name).'-'.($i + 1).'@mail.test',
                    'password' => 'DemoPass!'.($i + 1),
                    'additional_info' => 'Stok demo. Ganti di admin panel sebelum dijual.',
                    'is_sold' => false,
                ]);
            }
        }

        // ---- Flash Sale demo ----
        if (! Flashsale::exists() && isset($variantRefs['Netflix Premium Sharing|1 Bulan'])) {
            Flashsale::create([
                'name' => 'Netflix Promo Akhir Bulan',
                'product_variant_id' => $variantRefs['Netflix Premium Sharing|1 Bulan']->id,
                'flash_price' => 17000,
                'quota' => 20,
                'sold' => 0,
                'start_at' => now()->subHour(),
                'end_at' => now()->addDays(2),
                'is_active' => true,
            ]);
        }
        if (isset($variantRefs['CapCut Pro Private|1 Bulan'])) {
            Flashsale::firstOrCreate(
                ['product_variant_id' => $variantRefs['CapCut Pro Private|1 Bulan']->id],
                [
                    'name' => 'CapCut Lebih Hemat',
                    'flash_price' => 9900,
                    'quota' => 30,
                    'sold' => 5,
                    'start_at' => now()->subHour(),
                    'end_at' => now()->addDay(),
                    'is_active' => true,
                ]
            );
        }

        // ---- FAQs ----
        $faqs = [
            ['Apakah akun yang dijual legal?', 'Ya, semua akun yang kami jual diperoleh dari sumber resmi/legal dan memiliki garansi penuh sesuai durasi pembelian.'],
            ['Berapa lama akun dikirim setelah pembayaran?', 'Untuk produk dengan tag Auto-Delivery, akun akan langsung muncul di halaman invoice kamu dalam hitungan detik setelah pembayaran berhasil.'],
            ['Bagaimana jika akun bermasalah?', 'Hubungi admin via WhatsApp dengan menyertakan kode order. Akun akan kami ganti tanpa biaya tambahan selama masih dalam masa garansi.'],
            ['Metode pembayaran apa saja yang tersedia?', 'Kami menerima QRIS (semua e-wallet), Virtual Account (BCA/BNI/BRI/Mandiri), dan E-Wallet langsung (DANA/OVO/Gopay/ShopeePay) melalui Pakasir.'],
            ['Apakah saya bisa refund?', 'Refund hanya berlaku jika akun yang dikirim tidak dapat digunakan dan tidak bisa kami ganti dengan akun pengganti.'],
        ];
        foreach ($faqs as $i => [$q, $a]) {
            Faq::firstOrCreate(['question' => $q], [
                'answer' => $a,
                'sort_order' => $i,
                'is_active' => true,
            ]);
        }

        // ---- Articles ----
        $articles = [
            [
                'Tips Memilih Akun Streaming yang Aman',
                'Pelajari hal-hal penting sebelum membeli akun streaming sharing agar pengalaman menonton kamu nyaman & aman.',
                '<p>Akun streaming sharing memang lebih hemat. Tapi sebelum beli, pastikan kamu memperhatikan beberapa hal berikut:</p><ol><li>Cek garansi penuh durasi.</li><li>Pastikan penjual menyediakan akun pengganti jika bermasalah.</li><li>Hindari penjual yang tidak punya channel komunikasi resmi.</li></ol><p>Di AKHPREMIUM, semua produk sudah memenuhi kriteria di atas.</p>',
            ],
            [
                'Mengapa Auto-Delivery Lebih Cepat?',
                'Sistem auto-delivery memungkinkan akun kamu terkirim otomatis dalam hitungan detik tanpa perlu menunggu admin.',
                '<p>Sistem auto-delivery kami terhubung langsung dengan database stok ter-enkripsi. Begitu pembayaran berhasil terverifikasi oleh Pakasir, sistem otomatis mengambil 1 stok yang tersedia dan mengirimnya ke halaman invoice kamu.</p><p>Tidak perlu menunggu admin manual—semua berjalan 24/7.</p>',
            ],
            [
                'Cara Login Akun Sharing dengan Aman',
                'Beberapa langkah penting agar akun sharing kamu tidak terkena suspend atau bug login.',
                '<p>Tips login aman:</p><ul><li>Jangan ganti email/password akun sharing.</li><li>Pakai mode incognito jika sebelumnya pernah login akun lain.</li><li>Hubungi admin segera jika muncul tampilan tidak biasa.</li></ul>',
            ],
        ];
        foreach ($articles as [$title, $excerpt, $content]) {
            Article::firstOrCreate(
                ['title' => $title],
                [
                    'slug' => Article::makeSlug($title),
                    'excerpt' => $excerpt,
                    'content' => $content,
                    'is_published' => true,
                    'published_at' => now(),
                ]
            );
        }

        // ---- Testimoni demo (admin bisa tambah/edit/hapus dari panel) ----
        $testimonials = [
            ['Rian Pratama', 'Content Creator', 5, 'Beli akun CapCut Pro langsung kelar 3 detik. Nggak ribet, ga pakai cari admin. Recommended banget.'],
            ['Maya Saraswati', 'Mahasiswa', 5, 'Netflix-nya aman, garansi penuh, harga ramah kantong. Udah langganan 4 bulan tanpa kendala.'],
            ['Bagas Wirawan', 'Pelanggan Setia', 5, 'Sudah order 8 kali di sini. Auto-delivery-nya ngebut, support WA juga fast response. Mantap!'],
        ];
        foreach ($testimonials as $i => [$name, $role, $rating, $content]) {
            Testimonial::firstOrCreate(
                ['name' => $name],
                [
                    'role' => $role,
                    'rating' => $rating,
                    'content' => $content,
                    'is_active' => true,
                    'sort_order' => $i,
                ]
            );
        }
    }
}
