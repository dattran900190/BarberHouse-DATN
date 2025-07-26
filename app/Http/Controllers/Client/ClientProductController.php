<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductCategory;

class ClientProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();
        $globalCategories = ProductCategory::all();
        // ---- LỌC DANH MỤC ----
        if ($request->filled('category')) {
            $query->where('product_category_id', $request->category);
        }

        // ---- LỌC KHOẢNG GIÁ ----
        if ($request->filled('price_range')) {
            [$min, $max] = explode('-', $request->price_range);
            $query->whereBetween('price', [(int)$min * 1000, (int)$max * 1000]);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->with('variants')->latest()->paginate(8);

        return view('client.product', compact('products', 'globalCategories'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        $relatedProducts = Product::where('product_category_id', $product->product_category_id)
            ->where('id', '!=', $product->id)
            ->latest()
            ->take(8)
            ->get();

        // Lấy map id => url ảnh của từng biến thể
        $variantImages = $product->variants->mapWithKeys(function ($variant) {
            return [$variant->id => $variant->image ? asset('storage/' . $variant->image) : null];
        });

        return view('client.detailProduct', compact('product', 'relatedProducts', 'variantImages'));
    }
}
