<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class PostController extends Controller
{

    public function index(Request $request)
{
     $posts = Post::orderBy('created_at', 'desc')->get(); // Hiển thị bài mới nhất trước

    $query = Post::query();


    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where('title', 'like', '%' . $search . '%');
    }


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

     public function store(Request $request)
{
    $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255|unique:posts,slug',
        'content' => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        'status' => 'required|boolean',
        'published_at' => 'nullable|date',

    ];

    $messages = [
        'title.required' => 'Tiêu đề không được để trống.',
        'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
        'slug.max' => 'Slug không được vượt quá 255 ký tự.',
        'slug.unique' => 'Slug này đã được sử dụng, vui lòng chọn slug khác.',
        'content.required' => 'Nội dung bài viết không được để trống.',
        'image.image' => 'Tệp tải lên phải là hình ảnh.',
        'image.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif, svg, webp.',
        'image.max' => 'Kích thước ảnh không được vượt quá 2MB.',
        'status.required' => 'Trạng thái bài viết là bắt buộc.',
        'status.boolean' => 'Trạng thái không hợp lệ.',
        'published_at.date' => 'Ngày xuất bản phải là ngày hợp lệ.',

    ];

    $data = $request->validate($rules, $messages);

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

    return redirect()->route('admin.posts.index')->with('success', 'Bài viết đã được thêm thành công!');

}

public function edit(Post $post)
{
    return view('admin.posts.edit', compact('post'));
}

public function update(Request $request, Post $post)
    {  $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255|unique:posts,slug',
        'content' => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        'status' => 'required|boolean',
        'published_at' => 'nullable|date',
    ];

    $messages = [
        'title.required' => 'Tiêu đề không được để trống.',
        'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
        'slug.max' => 'Slug không được vượt quá 255 ký tự.',
        'slug.unique' => 'Slug này đã được sử dụng, vui lòng chọn slug khác.',
        'content.required' => 'Nội dung bài viết không được để trống.',
        'image.image' => 'Tệp tải lên phải là hình ảnh.',
        'image.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif, svg, webp.',
        'image.max' => 'Kích thước ảnh không được vượt quá 2MB.',
        'status.required' => 'Trạng thái bài viết là bắt buộc.',
        'status.boolean' => 'Trạng thái không hợp lệ.',
        'published_at.date' => 'Ngày xuất bản phải là ngày hợp lệ.',

    ];

    $data = $request->validate($rules, $messages);


         $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
$originalSlug = $data['slug'];
$counter = 1;

while (Post::where('slug', $data['slug'])->exists()) {
    $data['slug'] = $originalSlug . '-' . $counter;
    $counter++;
}
        // Nếu có upload ảnh mới, xóa ảnh cũ và lưu ảnh mới
        if ($request->hasFile('image')) {
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        $post->update($data);

        return redirect()->route('admin.posts.index')->with('success', 'Bài viết đã được cập nhật thành công!');
    }

public function destroy(Post $post)
{
    // Xóa hình ảnh nếu có
    if ($post->image && \Storage::disk('public')->exists($post->image)) {
        \Storage::disk('public')->delete($post->image);
    }

    // Xóa bài viết
    $post->delete();

    return redirect()->route('admin.posts.index')->with('success', 'Xóa bài viết thành công!');
}

}
