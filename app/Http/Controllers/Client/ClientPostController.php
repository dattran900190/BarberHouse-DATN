<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Post;

class ClientPostController extends Controller {
  public function index()
{
    // Bài viết nổi bật (is_featured = true)
    $featuredPosts = Post::where('status', 1)->latest()->get();
    $featuredPosts = Post::where('status', 1)
                         ->where('is_featured', true)
                         ->latest('published_at')
                         ->take(5)
                         ->get();

    // Bài viết bình thường (is_featured = false)
    $normalPosts = Post::where('status', 1)->latest()->get();
    $normalPosts = Post::where('status', 1)
                       ->where('is_featured', false)
                       ->latest('published_at')
                       ->paginate(10); // thêm phân trang nếu cần

    return view('client.posts', compact('featuredPosts', 'normalPosts'));
}


public function detail($slug)
{
    $post = Post::where('slug', $slug)->firstOrFail();

    $relatedPosts = Post::where('id', '!=', $post->id)
                        ->where('status', 'published')
                        ->latest()
                        ->take(5)
                        ->get();

    return view('client.detailPost', compact('post', 'relatedPosts'));
}

}
