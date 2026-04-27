<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\PakasirService;
use App\Support\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(protected PakasirService $pakasir) {}

    /**
     * GET /checkout/{product}/{variant}
     * Halaman form checkout instan (tanpa login).
     */
    public function show(Product $product, ProductVariant $variant): View|RedirectResponse
    {
        abort_if($variant->product_id !== $product->id, 404);

        $available = $variant->availableStocks()->count();

        if ($available <= 0 && $product->is_auto_send) {
            return redirect()
                ->route('products.show', $product)
                ->with('error', 'Mohon maaf, stok untuk varian ini sedang kosong.');
        }

        return view('checkout', [
            'product' => $product,
            'variant' => $variant,
            'available' => $available,
        ]);
    }

    /**
     * POST /checkout
     * Validasi input, kunci harga di server, buat Order, redirect ke Pakasir.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'product_variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:32', 'regex:/^[0-9+\- ]+$/'],
        ]);

        /** @var ProductVariant $variant */
        $variant = ProductVariant::with('product')->findOrFail($data['product_variant_id']);

        abort_if(
            $variant->product_id !== (int) $data['product_id'],
            422,
            'Varian tidak cocok dengan produk.'
        );

        // Hitung harga ULANG di server — jangan percaya input client.
        // Pakai harga flashsale kalau sedang aktif untuk varian ini.
        $amount = $variant->effectivePrice();
        $fee = 0;
        $total = $amount + $fee;

        $order = DB::transaction(function () use ($variant, $data, $amount, $fee, $total) {
            return Order::create([
                'order_code' => $this->generateOrderCode(),
                'product_id' => $variant->product_id,
                'product_variant_id' => $variant->id,
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'amount' => $amount,
                'fee' => $fee,
                'total_payment' => $total,
                'status' => Order::STATUS_PENDING,
                'expired_at' => now()->addMinutes(
                    (int) config('pakasir.order_expiry_minutes', 60)
                ),
            ]);
        });

        Audit::log('order.created', $order, [
            'amount' => $amount,
            'variant' => $variant->name,
            'product' => $variant->product?->name,
        ]);

        if (! $this->pakasir->isConfigured()) {
            return redirect()
                ->route('invoice.show', $order->order_code)
                ->with('warning', 'Payment gateway belum dikonfigurasi. Hubungi admin.');
        }

        $paymentUrl = $this->pakasir->buildPaymentUrl(
            $order,
            route('invoice.show', $order->order_code)
        );

        return redirect()->away($paymentUrl);
    }

    protected function generateOrderCode(): string
    {
        // Contoh: AKH-20260427-AB12CD
        return 'AKH-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
    }
}
