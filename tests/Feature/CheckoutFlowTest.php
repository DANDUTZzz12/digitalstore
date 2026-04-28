<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Services\OrderFulfillment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_lists_products(): void
    {
        $cat = Category::create(['name' => 'Streaming', 'slug' => 'streaming']);
        $p = Product::create([
            'name' => 'Netflix Test',
            'price' => 25000,
            'is_auto_send' => true,
            'category_id' => $cat->id,
        ]);
        ProductVariant::create([
            'product_id' => $p->id,
            'name' => '1 Bulan',
            'price' => 25000,
        ]);

        $this->get('/')->assertStatus(200)->assertSee('Netflix Test');
    }

    public function test_checkout_creates_pending_order_and_validates_input(): void
    {
        $cat = Category::create(['name' => 'Streaming', 'slug' => 'streaming']);
        $p = Product::create([
            'name' => 'X',
            'price' => 1000,
            'is_auto_send' => true,
            'category_id' => $cat->id,
        ]);
        $v = ProductVariant::create([
            'product_id' => $p->id,
            'name' => '1 Bulan',
            'price' => 1000,
        ]);
        Stock::create([
            'product_variant_id' => $v->id,
            'email_or_phone' => 'x@y.test',
            'password' => 'secret',
            'is_sold' => false,
        ]);

        // Validasi gagal kalau email kosong
        $this->post('/checkout', [
            'product_id' => $p->id,
            'product_variant_id' => $v->id,
        ])->assertSessionHasErrors(['customer_email']);

        $this->assertSame(0, Order::count());

        // Sukses
        $this->post('/checkout', [
            'product_id' => $p->id,
            'product_variant_id' => $v->id,
            'customer_email' => 'buyer@test.com',
        ])->assertRedirect();

        $order = Order::firstOrFail();
        $this->assertSame(Order::STATUS_PENDING, $order->status);
        $this->assertSame(1000, $order->total_payment);
        $this->assertSame('buyer@test.com', $order->customer_email);
    }

    public function test_fulfillment_assigns_stock_atomically_and_is_idempotent(): void
    {
        $cat = Category::create(['name' => 'X', 'slug' => 'x']);
        $p = Product::create(['name' => 'Y', 'price' => 100, 'is_auto_send' => true, 'category_id' => $cat->id]);
        $v = ProductVariant::create(['product_id' => $p->id, 'name' => '1', 'price' => 100]);
        $s = Stock::create([
            'product_variant_id' => $v->id,
            'email_or_phone' => 'a@b.test',
            'password' => 'p',
            'is_sold' => false,
        ]);
        $order = Order::create([
            'order_code' => 'TEST-1',
            'product_id' => $p->id,
            'product_variant_id' => $v->id,
            'amount' => 100,
            'fee' => 0,
            'total_payment' => 100,
            'status' => Order::STATUS_PENDING,
        ]);

        /** @var OrderFulfillment $svc */
        $svc = app(OrderFulfillment::class);

        $this->assertTrue($svc->markPaidAndAssignStock($order));
        $order->refresh();
        $s->refresh();
        $this->assertSame(Order::STATUS_PAID, $order->status);
        $this->assertSame($s->id, $order->stock_id);
        $this->assertTrue($s->is_sold);

        // Idempotent — call again, no side effects
        $this->assertTrue($svc->markPaidAndAssignStock($order));
        $this->assertSame(1, Stock::where('is_sold', true)->count());
    }

    public function test_fulfillment_rescues_paid_order_with_missing_stock(): void
    {
        // Skenario: admin sebelumnya manual mark order PAID tanpa lewat
        // OrderFulfillment, lalu baru memasukkan stok untuk varian itu.
        // Pemanggilan ulang harus meng-assign stok ke order yang sudah paid.
        $cat = Category::create(['name' => 'X', 'slug' => 'x']);
        $p = Product::create(['name' => 'Y', 'price' => 100, 'is_auto_send' => true, 'category_id' => $cat->id]);
        $v = ProductVariant::create(['product_id' => $p->id, 'name' => '1', 'price' => 100]);

        $order = Order::create([
            'order_code' => 'TEST-RESCUE',
            'product_id' => $p->id,
            'product_variant_id' => $v->id,
            'amount' => 100,
            'fee' => 0,
            'total_payment' => 100,
            'status' => Order::STATUS_PAID, // sudah paid, tapi stock_id null
        ]);

        // Stok ditambahkan SETELAH order paid
        $s = Stock::create([
            'product_variant_id' => $v->id,
            'email_or_phone' => 'rescue@b.test',
            'password' => 'p',
            'is_sold' => false,
        ]);

        /** @var OrderFulfillment $svc */
        $svc = app(OrderFulfillment::class);
        $this->assertTrue($svc->markPaidAndAssignStock($order));

        $order->refresh();
        $s->refresh();
        $this->assertSame($s->id, $order->stock_id);
        $this->assertTrue($s->is_sold);
    }

    public function test_invoice_page_shows_credentials_only_when_paid(): void
    {
        $cat = Category::create(['name' => 'X', 'slug' => 'x']);
        $p = Product::create(['name' => 'Y', 'price' => 100, 'is_auto_send' => true, 'category_id' => $cat->id]);
        $v = ProductVariant::create(['product_id' => $p->id, 'name' => '1', 'price' => 100]);
        $s = Stock::create([
            'product_variant_id' => $v->id,
            'email_or_phone' => 'creds@hidden.test',
            'password' => 'SuperSecret',
            'is_sold' => false,
        ]);
        $order = Order::create([
            'order_code' => 'INV-1',
            'product_id' => $p->id,
            'product_variant_id' => $v->id,
            'amount' => 100,
            'fee' => 0,
            'total_payment' => 100,
            'status' => Order::STATUS_PENDING,
            'customer_email' => 'b@b.test',
        ]);

        // Pending: kredensial TIDAK boleh muncul.
        $this->get('/invoice/INV-1')
            ->assertOk()
            ->assertDontSee('SuperSecret')
            ->assertDontSee('creds@hidden.test');

        // Paid + stok ter-assign: kredensial muncul.
        $order->update(['status' => Order::STATUS_PAID, 'stock_id' => $s->id]);
        $this->get('/invoice/INV-1')
            ->assertOk()
            ->assertSee('SuperSecret')
            ->assertSee('creds@hidden.test');
    }
}
