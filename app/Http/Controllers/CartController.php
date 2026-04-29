<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Cart sederhana untuk user login.
 *
 * Karena gateway pembayaran (Pakasir) hanya menerima 1 order per transaksi,
 * cart di sini berfungsi sebagai "saved-for-later list". Tiap item punya
 * tombol "Bayar Sekarang" yang langsung mengarah ke checkout instan untuk
 * varian tersebut. Guest TIDAK diijinkan mengakses cart (qty = 1, beli langsung).
 */
class CartController extends Controller
{
    public function index(): View
    {
        $items = CartItem::with(['product', 'variant'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('cart.index', compact('items'));
    }

    public function add(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        /** @var ProductVariant $variant */
        $variant = ProductVariant::with('product')->findOrFail($data['product_variant_id']);

        $userId = Auth::id();

        $item = CartItem::firstOrNew([
            'user_id' => $userId,
            'product_variant_id' => $variant->id,
        ]);

        $item->product_id = $variant->product_id;
        $item->quantity = min(10, ($item->quantity ?? 0) + (int) ($data['quantity'] ?? 1));
        $item->save();

        return redirect()->route('cart.index')
            ->with('success', "{$variant->product?->name} ({$variant->name}) ditambahkan ke keranjang.");
    }

    public function update(Request $request, CartItem $item): RedirectResponse
    {
        abort_unless($item->user_id === Auth::id(), 403);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $item->quantity = (int) $data['quantity'];
        $item->save();

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function destroy(CartItem $item): RedirectResponse
    {
        abort_unless($item->user_id === Auth::id(), 403);
        $item->delete();

        return back()->with('success', 'Item dihapus dari keranjang.');
    }
}
