<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Post;

class ClientPostController extends Controller
{
    public function index()
    {
        // Bài viết nổi bật
        $featuredPosts = Post::where('status', 'published')
            ->where('is_featured', true)
            ->latest('published_at')
            ->take(5)
            ->get();

        // Bài viết thường
        $normalPosts = Post::where('status', 'published')
            ->where('is_featured', false)
            ->latest('published_at')
            ->paginate(10);

        return view('client.posts', compact('featuredPosts', 'normalPosts'));
    }


    public function detail($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        $relatedPosts = Post::where('id', '!=', $post->id)
            ->where('status', 'published') // ✅ sửa lại so với trước là 1
            ->latest()
            ->take(5)
            ->get();


        return view('client.detailPost', compact('post', 'relatedPosts'));
    }


}