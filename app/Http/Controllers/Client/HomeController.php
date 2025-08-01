<?php

namespace App\Http\Controllers\Client;

use App\Models\Post;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\CustomerImage;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    public function index()
    {
        // Bài viết nổi bật (status = published, is_featured = true)
        $featuredPosts = Post::where('status', 'published')
            ->where('is_featured', true)
            ->latest('published_at')
            ->take(5)
            ->get();

        $normalPosts = Post::where('status', 'published')
            ->where('is_featured', false)
            ->latest('published_at')
            ->take(4)
            ->get();

        // Sản phẩm
        $products = Product::with('variants')->latest()->take(8)->get();

        // ảnh khách hàng
        $customerImages = CustomerImage::where('status', true)
            ->latest()
            ->take(9)
            ->get();

        return view('client.home', compact(
            'featuredPosts',
            'normalPosts',
            'products',
            'customerImages',
        ));
    }

    public function privacyPolicy()
    {
        return view('client.privacyPolicy');
    }

    public function tradingPolicy()
    {
        return view('client.tradingPolicy');
    }

    public function shippingPolicy()
    {
        return view('client.shippingPolicy');
    }

    public function warrantyReturnPolicy()
    {
        return view('client.warrantyReturnPolicy');
    }
}
