<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::orderBy('name')->get();

        $query = Product::query()
            ->with(['category', 'variants'])
            ->orderBy('name');

        if ($slug = $request->query('kategori')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $slug));
        }

        if ($search = trim((string) $request->query('q'))) {
            $query->where('name', 'like', '%'.$search.'%');
        }

        $products = $query->get();

        return view('welcome', [
            'categories' => $categories,
            'products' => $products,
            'activeCategory' => $slug ?? null,
            'searchQuery' => $search ?? '',
        ]);
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'variants' => function ($q) {
            $q->withCount(['stocks as available_stocks_count' => function ($sub) {
                $sub->where('is_sold', false);
            }]);
        }]);

        return view('product', [
            'product' => $product,
        ]);
    }
}
