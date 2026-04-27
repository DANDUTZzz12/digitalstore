<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Flashsale;
use App\Models\Order;
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
            ->orderBy('is_best_seller', 'desc')
            ->orderBy('sold_count', 'desc')
            ->orderBy('name');

        if ($slug = $request->query('kategori')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $slug));
        }

        if ($search = trim((string) $request->query('q'))) {
            $query->where('name', 'like', '%'.$search.'%');
        }

        $products = $query->get();

        $flashsales = Flashsale::active()
            ->with(['variant.product.category'])
            ->orderBy('end_at')
            ->get();

        $articles = Article::published()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        return view('welcome', [
            'categories' => $categories,
            'products' => $products,
            'activeCategory' => $slug ?? null,
            'searchQuery' => $search ?? '',
            'flashsales' => $flashsales,
            'articles' => $articles,
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

    public function faq(): View
    {
        return view('pages.faq', [
            'faqs' => Faq::active()->get(),
        ]);
    }

    public function howToOrder(): View
    {
        return view('pages.how-to-order');
    }

    public function terms(): View
    {
        return view('pages.terms');
    }

    public function articleIndex(): View
    {
        return view('pages.articles', [
            'articles' => Article::published()
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->paginate(12),
        ]);
    }

    public function articleShow(Article $article): View
    {
        abort_unless($article->is_published, 404);

        return view('pages.article-show', [
            'article' => $article,
            'related' => Article::published()
                ->where('id', '!=', $article->id)
                ->orderByDesc('published_at')
                ->limit(3)
                ->get(),
        ]);
    }

    public function cekInvoice(Request $request)
    {
        $code = trim((string) $request->query('order_code', ''));
        if ($code !== '') {
            $exists = Order::where('order_code', $code)->exists();
            if ($exists) {
                return redirect()->route('invoice.show', $code);
            }

            return view('pages.cek-invoice', [
                'error' => 'Kode order tidak ditemukan. Pastikan kamu memasukkan kode yang benar.',
                'code' => $code,
            ]);
        }

        return view('pages.cek-invoice', ['code' => '']);
    }
}
