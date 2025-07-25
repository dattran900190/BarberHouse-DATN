<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ClientProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::whereHas('variants', function ($q) {
            $q->whereNotNull('volume_id')
                ->whereHas('volume', function ($q2) {
                    $q2->whereNull('deleted_at')
                        ->where('name', '!=', 'Không rõ');
                });
        });

        // ---- Tìm kiếm theo tên ----
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // ---- LỌC DANH MỤC ----
        if ($request->filled('category')) {
            $query->where('product_category_id', $request->category);
        }

        // ---- LỌC KHOẢNG GIÁ ----
        if ($request->filled('price_range')) {
            [$min, $max] = explode('-', $request->price_range);
            $query->whereBetween('price', [(int)$min * 1000, (int)$max * 1000]);
        }

        $products = $query->with('variants')->latest()->paginate(8);

        return view('client.product', compact('products'));
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
