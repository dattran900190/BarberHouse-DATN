<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Post;

class ClientPostController extends Controller {
  public function index() {
    $posts = Post::where('status',1)->latest()->get();
    return view('client.posts', compact('posts'));
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
