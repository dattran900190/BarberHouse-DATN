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
    $filter = $request->input('filter', 'all');
    $query = Post::query();

    // Xử lý trạng thái bị xoá mềm
    if ($filter === 'deleted') {
        $query = Post::onlyTrashed();
    } elseif ($filter === 'all') {
        $query = Post::withTrashed();
    }

    // Trạng thái bài viết
    if ($filter === 'active') {
        $query->where('status', 'published');
    } elseif ($filter === 'inactive') {
        $query->where('status', 'draft');
    }

    // Tìm kiếm
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where('title', 'like', '%' . $search . '%');
    }

    // Phân trang
    $posts = $query->orderBy('created_at', 'desc')->paginate(10);

    // Lấy featured & normal (nếu cần hiển thị ở nơi khác)
    $featuredPosts = Post::where('is_featured', true)
        ->where('status', 'published')
        ->latest('published_at')
        ->take(5)
        ->get();

    $normalPosts = Post::where('is_featured', false)
        ->where('status', 'published')
        ->latest('published_at')
        ->take(5)
        ->get();

    return view('admin.posts.index', compact('posts', 'featuredPosts', 'normalPosts', 'filter'));
}




    public function show($post)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('posts.index')->with('error', 'Bạn không có quyền truy cập.');
        }

        $post = Post::withTrashed()->findOrFail($post);

        return view('admin.posts.show', compact('post'));
    }

    public function create()
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('posts.index')->with('error', 'Bạn không có quyền truy cập.');
        }
        $authors = User::all(); // hoặc Author::all() nếu bạn dùng model riêng
        return view('admin.posts.create', compact('authors'));
    }

    public function store(PostRequest $request)
    {
        $data = $request->validated();
        $data['is_featured'] = $request->input('is_featured') == 1 ? 1 : 0;


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
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('posts.index')->with('error', 'Bạn không có quyền truy cập.');
        }
        return view('admin.posts.edit', compact('post'));
    }

    public function update(PostRequest $request, Post $post)
    {
        $data = $request->validated();
        $data['is_featured'] = $request->input('is_featured') == 1 ? 1 : 0;

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


    public function forceDelete($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa bài viết.'
            ]);
        }

        $post = Post::withTrashed()->find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài viết.'
            ]);
        }

        // ⚠️ Nếu KHÔNG có quan hệ comments => bỏ đoạn này đi

        // Xoá hình nếu có
        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        $post->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xoá vĩnh viễn bài viết.'
        ]);
    }


    public function softDelete($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xoá bài viết.'
            ]);
        }

        $post = Post::findOrFail($id);
        $post->delete(); // gán deleted_at

        return response()->json([
            'success' => true,
            'message' => 'Đã xoá mềm bài viết.'
        ]);
    }
    public function restore($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền khôi phục bài viết.'
            ]);
        }

        $post = Post::withTrashed()->findOrFail($id);
        $post->restore();

        return response()->json([
            'success' => true,
            'message' => 'Khôi phục bài viết thành công.'
        ]);
    }


}
