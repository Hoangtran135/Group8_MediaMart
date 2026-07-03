<?php

namespace App\Http\Controllers;

use App\Http\Requests\RatingRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Rating;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function index(Request $request)
    {
        $categoryId = $request->integer('category_id') ?: null;
        $order      = $request->get('order', 'newest');
        $fromPrice  = $request->filled('from_price') ? (float) $request->get('from_price') : null;
        $toPrice    = $request->filled('to_price') ? (float) $request->get('to_price') : null;

        $products = $this->productService->listByCategory($categoryId, $order, $fromPrice, $toPrice);
        $category = $categoryId ? Category::find($categoryId) : null;

        return view('frontend.products.index', compact('products', 'category', 'order', 'categoryId', 'fromPrice', 'toPrice'));
    }

    public function detail(int $id)
    {
        $product   = Product::with(['ratings.customer', 'category'])->findOrFail($id);
        $avgRating = $product->ratings->avg('star') ?? 0;
        $related   = Product::where('category_id', $product->category_id)
                             ->where('id', '!=', $id)
                             ->latest()
                             ->take(6)
                             ->get();

        $customer      = Auth::guard('customer')->user();
        $canRate       = false;
        $alreadyRated  = false;

        if ($customer) {
            $alreadyRated = $customer->hasRated($id);
            $canRate      = !$alreadyRated && $customer->hasPurchased($id);
        }

        return view('frontend.products.detail', compact('product', 'avgRating', 'related', 'canRate', 'alreadyRated'));
    }

    public function rating(RatingRequest $request, int $id)
    {
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            return back()->with('error', 'Vui lòng đăng nhập để đánh giá.');
        }

        if ($customer->hasRated($id)) {
            return back()->with('error', 'Bạn đã đánh giá sản phẩm này rồi.');
        }

        if (!$customer->hasPurchased($id)) {
            return back()->with('error', 'Bạn cần mua sản phẩm này trước khi đánh giá.');
        }

        Rating::create([
            'product_id'  => $id,
            'customer_id' => $customer->id,
            'star'        => $request->star,
            'review'      => $request->review,
        ]);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá!');
    }
}
