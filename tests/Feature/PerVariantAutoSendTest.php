<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SiteSetting;
use App\Models\Stock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerVariantAutoSendTest extends TestCase
{
    use RefreshDatabase;

    private function makeProductWithVariant(bool $productAutoSend, ?bool $variantAutoSend): ProductVariant
    {
        static $i = 0;
        $i++;
        $cat = Category::create(['name' => "S{$i}", 'slug' => "s-{$i}"]);
        $p = Product::create([
            'name' => 'X',
            'price' => 1000,
            'is_auto_send' => $productAutoSend,
            'category_id' => $cat->id,
        ]);

        return ProductVariant::create([
            'product_id' => $p->id,
            'name' => '1B',
            'price' => 1000,
            'is_auto_send' => $variantAutoSend,
        ]);
    }

    public function test_variant_inherits_product_auto_send_when_null(): void
    {
        $v = $this->makeProductWithVariant(productAutoSend: true, variantAutoSend: null);
        $this->assertTrue($v->isAutoSend());

        $v2 = $this->makeProductWithVariant(productAutoSend: false, variantAutoSend: null);
        $this->assertFalse($v2->isAutoSend());
    }

    public function test_variant_overrides_product_auto_send(): void
    {
        // Product auto, varian manual
        $v = $this->makeProductWithVariant(productAutoSend: true, variantAutoSend: false);
        $this->assertFalse($v->isAutoSend());

        // Product manual, varian auto
        $v2 = $this->makeProductWithVariant(productAutoSend: false, variantAutoSend: true);
        $this->assertTrue($v2->isAutoSend());
    }

    public function test_manual_variant_checkout_allowed_without_stock(): void
    {
        $v = $this->makeProductWithVariant(productAutoSend: true, variantAutoSend: false);

        $response = $this->get(route('checkout.show', [$v->product, $v]));
        $response->assertOk();
        $response->assertSee($v->name);
    }

    public function test_auto_variant_blocks_checkout_when_stock_empty(): void
    {
        $v = $this->makeProductWithVariant(productAutoSend: false, variantAutoSend: true);
        // stok kosong → redirect ke product detail
        $response = $this->get(route('checkout.show', [$v->product, $v]));
        $response->assertRedirect(route('products.show', $v->product));
    }

    public function test_auto_variant_allows_checkout_when_stock_available(): void
    {
        $v = $this->makeProductWithVariant(productAutoSend: false, variantAutoSend: true);
        Stock::create([
            'product_variant_id' => $v->id,
            'email_or_phone' => 'a@x.com',
            'password' => 'pw',
            'additional_info' => null,
            'is_sold' => false,
        ]);

        $response = $this->get(route('checkout.show', [$v->product, $v]));
        $response->assertOk();
    }

    public function test_invoice_shows_whatsapp_button_with_prefilled_order_code(): void
    {
        $site = SiteSetting::current();
        $site->update(['wa_number' => '081234567890']);

        $v = $this->makeProductWithVariant(productAutoSend: true, variantAutoSend: null);
        $order = Order::create([
            'order_code' => 'AKH-TEST-WA001',
            'product_id' => $v->product_id,
            'product_variant_id' => $v->id,
            'customer_email' => 'a@x.com',
            'amount' => 1000,
            'fee' => 0,
            'total_payment' => 1000,
            'status' => Order::STATUS_PENDING,
        ]);

        $response = $this->get(route('invoice.show', $order->order_code));
        $response->assertOk();
        // Auto-prefix ke 62 (internasional format)
        $response->assertSee('wa.me/6281234567890', false);
        $response->assertSee('AKH-TEST-WA001', false);
        $response->assertSee('Hubungi Admin via WhatsApp');
    }
}
