<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Product;

class HomeController extends Controller
{
    public function test()
    {
        return view('layouts.AdminLayout');
    }
    public function index()
{
    // Bài viết nổi bật (status = published, is_featured = true)
    $featuredPosts = Post::where('status', 'draft')
        ->where('is_featured', true)
        ->latest('published_at')
        ->take(5)
        ->get();

    // Bài viết không nổi bật
    $normalPosts = Post::where('status', 'draft')
        ->where('is_featured', false)
        ->latest('published_at')
        ->take(8)
        ->get();

    // Sản phẩm
    $products = Product::with('variants')->latest()->take(8)->get();

    return view('client.home', compact('featuredPosts', 'normalPosts', 'products'));
}

}
