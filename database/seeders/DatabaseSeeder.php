<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
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
        User::updateOrCreate(
            ['email' => 'admin@akhpremium.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'), // ganti segera setelah deploy!
                'is_admin' => true,
            ]
        );

        // ---- Contoh kategori + produk + varian + stok ----
        $streaming = Category::updateOrCreate(['slug' => 'streaming'], ['name' => 'Streaming']);
        $editing = Category::updateOrCreate(['slug' => 'editing'], ['name' => 'Editing']);
        $musik = Category::updateOrCreate(['slug' => 'musik'], ['name' => 'Musik']);

        $netflix = Product::updateOrCreate(
            ['name' => 'Netflix Premium Sharing'],
            [
                'description' => "Akun Netflix Sharing 4K UHD.\nGaransi full durasi. No share password.",
                'price' => 25000,
                'is_auto_send' => true,
                'category_id' => $streaming->id,
            ]
        );

        $capcut = Product::updateOrCreate(
            ['name' => 'CapCut Pro Private'],
            [
                'description' => "CapCut Pro Private — 1 akun 1 user.\nBebas watermark, semua fitur pro aktif.",
                'price' => 15000,
                'is_auto_send' => true,
                'category_id' => $editing->id,
            ]
        );

        $spotify = Product::updateOrCreate(
            ['name' => 'Spotify Premium Family'],
            [
                'description' => 'Spotify Premium slot Family, bebas iklan dan bisa offline.',
                'price' => 10000,
                'is_auto_send' => true,
                'category_id' => $musik->id,
            ]
        );

        $variants = [
            [$netflix, '1 Bulan', 25000, 3],
            [$netflix, '3 Bulan', 65000, 1],
            [$capcut, '1 Bulan', 15000, 2],
            [$spotify, '1 Bulan', 10000, 0], // sengaja kosong supaya UI "Habis" keliatan
        ];

        foreach ($variants as [$product, $name, $price, $stockCount]) {
            $variant = ProductVariant::updateOrCreate(
                ['product_id' => $product->id, 'name' => $name],
                ['price' => $price]
            );

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
    }
}
