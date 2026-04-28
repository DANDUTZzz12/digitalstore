<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SiteSetting;
use App\Models\Stock;
use App\Services\FonnteWhatsApp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FonnteWhatsAppTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        SiteSetting::clearCache();
    }

    private function makeOrderWithStock(?string $phone = '6281234567890'): Order
    {
        $cat = Category::create(['name' => 'TestCat', 'slug' => 'fonnte-cat']);
        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'fonnte-prod',
            'price' => 1000,
            'is_auto_send' => true,
            'category_id' => $cat->id,
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => '1B',
            'price' => 1000,
        ]);
        $stock = Stock::create([
            'product_variant_id' => $variant->id,
            'email_or_phone' => 'akunjual@example.com',
            'password' => 'rahasia123',
            'additional_info' => 'PIN: 9999',
            'is_sold' => true,
            'sold_at' => now(),
        ]);

        return Order::create([
            'order_code' => 'AKH-FNT-'.uniqid(),
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'stock_id' => $stock->id,
            'customer_email' => 'pembeli@example.com',
            'customer_phone' => $phone,
            'amount' => 1000,
            'fee' => 0,
            'total_payment' => 1000,
            'status' => Order::STATUS_PAID,
            'paid_at' => now(),
        ]);
    }

    public function test_skips_when_toggle_off(): void
    {
        Http::fake();
        $site = SiteSetting::current();
        $site->update([
            'fonnte_auto_send_credentials' => false,
            'fonnte_api_key' => 'whatever',
        ]);

        $order = $this->makeOrderWithStock();
        $sent = app(FonnteWhatsApp::class)->sendCredentials($order->fresh(['stock', 'product', 'variant']));

        $this->assertFalse($sent);
        Http::assertNothingSent();
    }

    public function test_skips_when_api_key_empty(): void
    {
        Http::fake();
        $site = SiteSetting::current();
        $site->update([
            'fonnte_auto_send_credentials' => true,
            'fonnte_api_key' => null,
        ]);

        $order = $this->makeOrderWithStock();
        $sent = app(FonnteWhatsApp::class)->sendCredentials($order->fresh(['stock', 'product', 'variant']));

        $this->assertFalse($sent);
        Http::assertNothingSent();
    }

    public function test_skips_when_no_phone(): void
    {
        Http::fake();
        $site = SiteSetting::current();
        $site->update([
            'fonnte_auto_send_credentials' => true,
            'fonnte_api_key' => 'token123',
        ]);

        $order = $this->makeOrderWithStock(phone: null);
        $sent = app(FonnteWhatsApp::class)->sendCredentials($order->fresh(['stock', 'product', 'variant']));

        $this->assertFalse($sent);
        Http::assertNothingSent();
    }

    public function test_sends_with_default_template_when_active(): void
    {
        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => true, 'detail' => 'ok'], 200),
        ]);

        $site = SiteSetting::current();
        $site->update([
            'fonnte_auto_send_credentials' => true,
            'fonnte_api_key' => 'token123',
        ]);

        $order = $this->makeOrderWithStock();
        $sent = app(FonnteWhatsApp::class)->sendCredentials($order->fresh(['stock', 'product', 'variant']));

        $this->assertTrue($sent);
        Http::assertSent(function ($request) use ($order) {
            $body = $request->data();

            return $request->url() === FonnteWhatsApp::ENDPOINT
                && $body['target'] === '6281234567890'
                && str_contains($body['message'], $order->order_code)
                && str_contains($body['message'], 'akunjual@example.com')
                && str_contains($body['message'], 'rahasia123')
                && $request->header('Authorization')[0] === 'token123';
        });
    }

    public function test_normalizes_phone_with_leading_zero(): void
    {
        $svc = new FonnteWhatsApp;
        $this->assertSame('6281234567890', $svc->normalizePhone('081234567890'));
        $this->assertSame('6281234567890', $svc->normalizePhone('+62 812-3456-7890'));
        $this->assertSame('6281234567890', $svc->normalizePhone('6281234567890'));
        $this->assertSame('', $svc->normalizePhone(''));
    }

    public function test_handles_failed_response_gracefully(): void
    {
        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => false, 'reason' => 'invalid token'], 401),
        ]);

        $site = SiteSetting::current();
        $site->update([
            'fonnte_auto_send_credentials' => true,
            'fonnte_api_key' => 'token123',
        ]);

        $order = $this->makeOrderWithStock();
        $sent = app(FonnteWhatsApp::class)->sendCredentials($order->fresh(['stock', 'product', 'variant']));

        $this->assertFalse($sent);
    }
}
