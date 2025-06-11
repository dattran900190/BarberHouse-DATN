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

 public function store(BannerRequest $request)
{
    $validated = $request->validated();

    $path = null;
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('uploads/banners', 'public');
    }

    Banner::create([
        'title' => $validated['title'],
        'link_url' => $validated['link_url'] ?? null, // đúng field trong BannerRequest là link_url
        'image_url' => $path,
        'is_active' => $validated['is_active'] ?? 0,
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

public function update(BannerRequest $request, Banner $banner)
{
    $validated = $request->validated();

    $dataToUpdate = [
        'title' => $validated['title'],
        'link_url' => $validated['link_url'] ?? null,
        'is_active' => $validated['is_active'] ?? 0,
        'position' => $validated['position'] ?? null,
    ];

    if ($request->hasFile('image')) {
        if ($banner->image_url && Storage::disk('public')->exists($banner->image_url)) {
            Storage::disk('public')->delete($banner->image_url);
        }
        $dataToUpdate['image_url'] = $request->file('image')->store('uploads/banners', 'public');
    }

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
