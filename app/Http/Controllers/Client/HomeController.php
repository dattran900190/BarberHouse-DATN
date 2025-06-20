<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {

        // Lấy 5 tin tức mới nhất, chỉ lấy các bài đã được publish
        $posts = Post::where('status',1)->latest()->get();
            

        // Lấy thêm các bài còn lại (loại trừ 5 bài đầu)
        $normalPosts = Post::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->skip(5)
            ->take(8)
            ->get();

        // Lấy 8 sản phẩm mới nhất
        $products = Product::with('variants')->latest()->take(8)->get();
        return view('client.home', compact('posts', 'normalPosts', 'products'));
    }
}
