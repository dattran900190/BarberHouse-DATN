<?php
namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use App\Http\Requests\BannerRequest;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $banners = Banner::when($search, function ($query, $search) {
            return $query->where('title', 'like', '%' . $search . '%');
        })->orderBy('id', 'DESC')->paginate(5);

        return view('admin.banners.index', compact('banners'));
    }

     public function create()
    {
        return view('admin.banners.create');
    }

  public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'link' => 'nullable|url',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'status' => 'required|in:0,1',
    ]);

    // Upload image nếu có
    $path = null;
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('uploads/banners', 'public');
    }

    // Tạo bản ghi mới
    Banner::create([
        'title' => $validated['title'],
        'link_url' => $validated['link'] ?? null, // map đúng tên cột
        'image_url' => $path,
        'is_active' => $validated['status'], // map đúng tên cột
    ]);

    return redirect()->route('banners.index')->with('success', 'Đã thêm banner thành công!');
}

   public function show($id)
{
    $banner = Banner::findOrFail($id);
    return view('admin.banners.show', compact('banner'));
}


    public function edit(Banner $banner)
{
    return view('admin.banners.edit', compact('banner'));
}

public function update(Request $request, Banner $banner)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'link' => 'nullable|url',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'status' => 'required|in:0,1',
        'position' => 'nullable|string|max:50',
    ]);

    // Mảng dữ liệu để cập nhật
    $dataToUpdate = [
        'title' => $validated['title'],
        'link_url' => $validated['link'] ?? null,
        'is_active' => $validated['status'],
        'position' => $validated['position'] ?? null,
    ];

    // Nếu có file ảnh mới, upload ảnh và xóa ảnh cũ nếu tồn tại
    if ($request->hasFile('image')) {
        // Xóa ảnh cũ
        if ($banner->image_url && Storage::disk('public')->exists($banner->image_url)) {
            Storage::disk('public')->delete($banner->image_url);
        }
        // Upload ảnh mới
        $dataToUpdate['image_url'] = $request->file('image')->store('uploads/banners', 'public');
    }

    // Cập nhật banner
    $banner->update($dataToUpdate);

    return redirect()->route('banners.index')->with('success', 'Cập nhật banner thành công!');
}


    public function destroy(Banner $banner)
    {
        if ($banner->image_url && Storage::disk('public')->exists($banner->image_url)) {
            Storage::disk('public')->delete($banner->image_url);
        }

        $banner->delete();

        return redirect()->route('banners.index')->with('success', 'Xoá banner thành công');
    }
}
