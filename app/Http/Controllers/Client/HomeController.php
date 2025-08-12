<?php

namespace App\Http\Controllers\Client;

use App\Models\Post;
use App\Models\Banner;
use App\Models\Barber;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\CustomerImage;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    public function index()
    {
        // Lấy danh sách thợ cắt (chỉ trạng thái idle - đang làm việc)
        $barbers = Barber::with('branch')
            ->where('status', 'idle')
            ->latest()
            ->take(6) // giới hạn số lượng hiển thị
            ->get();
        // Bài viết nổi bật (status = published, is_featured = true)
        $featuredPosts = Post::where('status', 'published')
            ->where('is_featured', true)
            ->latest('published_at')
            ->take(6)
            ->get();

        $normalPosts = Post::where('status', 'published')
            ->where('is_featured', false)
            ->latest('published_at')
            ->take(4)
            ->get();

        // Sản phẩm
        $products = Product::with('variants')->latest()->take(9)->get();

        // ảnh khách hàng
        $customerImages = CustomerImage::where('status', true)
            ->latest()
            ->take(9)
            ->get();

        return view('client.home', compact(
            'barbers',
            'featuredPosts',
            'normalPosts',
            'products',
            'customerImages',
        ));
    }

    public function getBarbers()
    {
        $barbers = \App\Models\Barber::with('branch')
            ->where('status', 'idle')
            ->latest()
            ->take(6)
            ->get();
        return response()->json($barbers);
    }

    public function getProducts()
    {
        $products = \App\Models\Product::with('variants')->latest()->take(9)->get();
        return response()->json($products);
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
