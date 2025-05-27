<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class PostController extends Controller
{

   public function index(Request $request)


   {
    $query = Post::query();


    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where('title', 'like', '%' . $search . '%');
    }

    $posts = $query->orderBy('created_at', 'desc')->paginate(5); // Phân trang 10 bài mỗi trang

    return view('admin.posts.index', compact('posts'));
}



    public function show(Post $post)
{
    return view('admin.posts.show', compact('post'));
}

   public function create()
{
    $authors = User::all(); // hoặc Author::all() nếu bạn dùng model riêng
    return view('admin.posts.create', compact('authors'));
}

public function store(PostRequest $request)
{
    $data = $request->validated();

    $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
    $originalSlug = $data['slug'];
    $counter = 1;

    while (Post::where('slug', $data['slug'])->exists()) {
        $data['slug'] = $originalSlug . '-' . $counter;
        $counter++;
    }

    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('posts', 'public');
    }

    Post::create($data);

    return redirect()->route('posts.index')->with('success', 'Bài viết đã được thêm thành công!');
}


public function edit(Post $post)
{
    return view('admin.posts.edit', compact('post'));
}

public function update(PostRequest $request, Post $post)
{
    $data = $request->validated();

    $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
    $originalSlug = $data['slug'];
    $counter = 1;

    while (Post::where('slug', $data['slug'])->where('id', '!=', $post->id)->exists()) {
        $data['slug'] = $originalSlug . '-' . $counter;
        $counter++;
    }

    if ($request->hasFile('image')) {
        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }
        $data['image'] = $request->file('image')->store('posts', 'public');
    }

    $post->update($data);

    return redirect()->route('posts.index')->with('success', 'Bài viết đã được cập nhật thành công!');
}

public function destroy(Post $post)
{
    // Xóa hình ảnh nếu có
    if ($post->image && \Storage::disk('public')->exists($post->image)) {
        \Storage::disk('public')->delete($post->image);
    }

    // Xóa bài viết
    $post->delete();

    return redirect()->route('posts.index')->with('success', 'Xóa bài viết thành công!');
}

}
