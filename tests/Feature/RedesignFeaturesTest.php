<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Flashsale;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SiteSetting;
use App\Models\Stock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedesignFeaturesTest extends TestCase
{
    use RefreshDatabase;

    private function makeProductWithVariant(int $price = 25000): ProductVariant
    {
        $cat = Category::create(['name' => 'Streaming', 'slug' => 'streaming']);
        $p = Product::create([
            'name' => 'Netflix Test',
            'price' => $price,
            'is_auto_send' => true,
            'category_id' => $cat->id,
        ]);

        return ProductVariant::create([
            'product_id' => $p->id,
            'name' => '1 Bulan',
            'price' => $price,
        ]);
    }

    public function test_active_flashsale_overrides_price_at_checkout(): void
    {
        $variant = $this->makeProductWithVariant(25000);
        Stock::create([
            'product_variant_id' => $variant->id,
            'email_or_phone' => 'x@y.test',
            'password' => 'p',
            'is_sold' => false,
        ]);
        Flashsale::create([
            'product_variant_id' => $variant->id,
            'flash_price' => 17000,
            'quota' => 10,
            'sold' => 0,
            'start_at' => now()->subHour(),
            'end_at' => now()->addHour(),
            'is_active' => true,
        ]);

        // Checkout post — server harus pakai harga flash, bukan harga normal.
        $this->post('/checkout', [
            'product_id' => $variant->product_id,
            'product_variant_id' => $variant->id,
            'customer_email' => 'buyer@test.com',
        ])->assertRedirect();

        $order = Order::firstOrFail();
        $this->assertSame(17000, $order->amount);
        $this->assertSame(17000, $order->total_payment);
    }

    public function test_expired_flashsale_does_not_override_price(): void
    {
        $variant = $this->makeProductWithVariant(25000);
        Stock::create([
            'product_variant_id' => $variant->id,
            'email_or_phone' => 'a@b.test',
            'password' => 'p',
            'is_sold' => false,
        ]);
        Flashsale::create([
            'product_variant_id' => $variant->id,
            'flash_price' => 5000,
            'quota' => 10,
            'sold' => 0,
            'start_at' => now()->subDays(2),
            'end_at' => now()->subDay(), // sudah lewat
            'is_active' => true,
        ]);

        $this->post('/checkout', [
            'product_id' => $variant->product_id,
            'product_variant_id' => $variant->id,
            'customer_email' => 'buyer@test.com',
        ])->assertRedirect();

        $order = Order::firstOrFail();
        $this->assertSame(25000, $order->amount);
    }

    public function test_flashsale_quota_exhausted_falls_back_to_normal_price(): void
    {
        $variant = $this->makeProductWithVariant(25000);
        Stock::create([
            'product_variant_id' => $variant->id,
            'email_or_phone' => 'a@b.test',
            'password' => 'p',
            'is_sold' => false,
        ]);
        Flashsale::create([
            'product_variant_id' => $variant->id,
            'flash_price' => 5000,
            'quota' => 5,
            'sold' => 5, // habis
            'start_at' => now()->subHour(),
            'end_at' => now()->addHour(),
            'is_active' => true,
        ]);

        $this->post('/checkout', [
            'product_id' => $variant->product_id,
            'product_variant_id' => $variant->id,
            'customer_email' => 'buyer@test.com',
        ])->assertRedirect();

        $order = Order::firstOrFail();
        $this->assertSame(25000, $order->amount);
    }

    public function test_info_pages_render(): void
    {
        $this->get('/faq')->assertOk();
        $this->get('/cara-pemesanan')->assertOk();
        $this->get('/ketentuan-order')->assertOk();
        $this->get('/cek-invoice')->assertOk();
        $this->get('/artikel')->assertOk();
    }

    public function test_faq_question_renders_in_faq_page(): void
    {
        Faq::create([
            'question' => 'Apakah aman?',
            'answer' => 'Ya aman.',
            'is_active' => true,
        ]);

        $this->get('/faq')->assertSee('Apakah aman?');
    }

    public function test_inactive_faq_is_hidden(): void
    {
        Faq::create(['question' => 'Hidden Q', 'answer' => '...', 'is_active' => false]);
        $this->get('/faq')->assertDontSee('Hidden Q');
    }

    public function test_published_article_visible_unpublished_returns_404(): void
    {
        $pub = Article::create([
            'title' => 'Public Article',
            'slug' => 'public-article',
            'content' => '<p>hello</p>',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);
        $unpub = Article::create([
            'title' => 'Draft Article',
            'slug' => 'draft-article',
            'content' => '<p>draft</p>',
            'is_published' => false,
        ]);

        $this->get('/artikel')->assertSee('Public Article')->assertDontSee('Draft Article');
        $this->get('/artikel/'.$pub->slug)->assertOk()->assertSee('Public Article');
        $this->get('/artikel/'.$unpub->slug)->assertNotFound();
    }

    public function test_cek_invoice_redirects_to_invoice_when_code_exists(): void
    {
        $variant = $this->makeProductWithVariant(10000);
        Order::create([
            'order_code' => 'AKH-TEST-001',
            'product_id' => $variant->product_id,
            'product_variant_id' => $variant->id,
            'amount' => 10000,
            'fee' => 0,
            'total_payment' => 10000,
            'status' => Order::STATUS_PENDING,
        ]);

        $this->get('/cek-invoice?order_code=AKH-TEST-001')
            ->assertRedirect('/invoice/AKH-TEST-001');
    }

    public function test_cek_invoice_shows_error_when_code_not_found(): void
    {
        $this->get('/cek-invoice?order_code=NOT-EXIST')
            ->assertOk()
            ->assertSee('tidak ditemukan');
    }

    public function test_site_settings_singleton_returns_same_row(): void
    {
        $a = SiteSetting::current();
        $a->store_name = 'Custom';
        $a->save();

        $b = SiteSetting::current();
        $this->assertSame($a->id, $b->id);
        $this->assertSame('Custom', $b->store_name);
    }

    public function test_homepage_renders_flashsale_when_active(): void
    {
        $variant = $this->makeProductWithVariant(25000);
        Flashsale::create([
            'product_variant_id' => $variant->id,
            'flash_price' => 17000,
            'quota' => 10,
            'sold' => 0,
            'start_at' => now()->subHour(),
            'end_at' => now()->addHour(),
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('FLASH SALE')
            ->assertSee('17.000');
    }
}
