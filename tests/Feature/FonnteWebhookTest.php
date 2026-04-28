<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FonnteWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        SiteSetting::clearCache();
    }

    private function configureSite(?string $secret = 'shh-secret-123', ?string $admin = null, ?string $apiKey = 'tok'): void
    {
        SiteSetting::current()->update([
            'fonnte_webhook_secret' => $secret,
            'fonnte_admin_number' => $admin,
            'fonnte_api_key' => $apiKey,
        ]);
        SiteSetting::clearCache();
    }

    private function makeOrder(string $code = 'AKH-FNT-WEBHOOK-1', string $phone = '6285211111111'): Order
    {
        $cat = Category::create(['name' => 'C', 'slug' => 'fnt-cat']);
        $product = Product::create([
            'name' => 'P',
            'slug' => 'fnt-prod',
            'price' => 1000,
            'category_id' => $cat->id,
        ]);
        $variant = ProductVariant::create(['product_id' => $product->id, 'name' => 'v', 'price' => 1000]);

        return Order::create([
            'order_code' => $code,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'customer_email' => 'c@x.com',
            'customer_phone' => $phone,
            'amount' => 1000,
            'fee' => 0,
            'total_payment' => 1000,
            'status' => Order::STATUS_PAID,
            'paid_at' => now(),
        ]);
    }

    public function test_disabled_when_secret_empty(): void
    {
        $this->configureSite(secret: null);

        $resp = $this->postJson('/webhooks/fonnte', ['sender' => '6285211111111', 'message' => 'halo']);
        $resp->assertStatus(403)->assertJsonPath('reason', 'webhook_disabled');
    }

    public function test_rejects_invalid_token(): void
    {
        $this->configureSite();

        $resp = $this->withHeaders(['X-Fonnte-Token' => 'wrong'])
            ->postJson('/webhooks/fonnte', ['sender' => '6285211111111', 'message' => 'halo']);

        $resp->assertStatus(401)->assertJsonPath('reason', 'invalid_token');
    }

    public function test_accepts_with_valid_token_and_logs(): void
    {
        Http::fake();
        $this->configureSite();

        $order = $this->makeOrder('AKH-FNT-MATCH-1');

        $resp = $this->withHeaders(['X-Fonnte-Token' => 'shh-secret-123'])
            ->post('/webhooks/fonnte', [
                'sender' => '6285211111111',
                'name' => 'Budi',
                'message' => 'Order saya AKH-FNT-MATCH-1 belum masuk?',
            ]);

        $resp->assertStatus(200)->assertJsonPath('ok', true);
        Http::assertNothingSent(); // no admin number set, so no forward.
    }

    public function test_forwards_to_admin_when_admin_number_set(): void
    {
        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => true], 200),
        ]);
        $this->configureSite(admin: '085211923457');

        $this->makeOrder('AKH-FNT-MATCH-2');

        $resp = $this->withHeaders(['X-Fonnte-Token' => 'shh-secret-123'])
            ->post('/webhooks/fonnte', [
                'sender' => '6285211111111',
                'name' => 'Andi',
                'message' => 'AKH-FNT-MATCH-2 mohon dibantu',
            ]);

        $resp->assertStatus(200);
        Http::assertSent(function ($request) {
            $body = $request->data();

            return $body['target'] === '6285211923457'
                && str_contains($body['message'], 'AKH-FNT-MATCH-2')
                && str_contains($body['message'], 'Andi');
        });
    }

    public function test_invalid_payload_returns_422(): void
    {
        $this->configureSite();

        $resp = $this->withHeaders(['X-Fonnte-Token' => 'shh-secret-123'])
            ->post('/webhooks/fonnte', ['sender' => '', 'message' => '']);

        $resp->assertStatus(422)->assertJsonPath('reason', 'invalid_payload');
    }
}
