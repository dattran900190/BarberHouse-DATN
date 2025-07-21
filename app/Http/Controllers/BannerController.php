<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use App\Http\Requests\BannerRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter', 'all'); // Mặc định là 'all'

        // Bắt đầu với builder phù hợp theo filter
        // Tạo builder gốc
        $query = Banner::query();

        // Xử lý lọc theo trạng thái
        if ($filter === 'deleted') {
            $query = Banner::onlyTrashed();
        } elseif ($filter === 'all') {
            $query = Banner::withTrashed();
        } elseif ($filter === 'inactive') {
            $query->where('is_active', 0);
        } elseif ($filter === 'active') {
            $query->where('is_active', 1);
        }

        // Áp dụng tìm kiếm nếu có
        if ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        // Lấy kết quả phân trang
        $banners = $query->orderBy('id', 'DESC')->paginate(5);

        // Truyền thêm filter để giữ trạng thái lọc ở view
        return view('admin.banners.index', compact('banners', 'filter', 'search'));
    }


    public function create()
    {

        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('banners.index')->with('error', 'Bạn không có quyền truy cập.');
        }
        return view('admin.banners.create');
    }

    public function store(BannerRequest $request)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('banners.index')->with('error', 'Bạn không có quyền truy cập.');
        }
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
        $banner = Banner::withTrashed()->findOrFail($id);
        return view('admin.banners.show', compact('banner'));
    }


    public function edit(Banner $banner)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('banners.index')->with('error', 'Bạn không có quyền truy cập.');
        }
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(BannerRequest $request, Banner $banner)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('banners.index')->with('error', 'Bạn không có quyền truy cập.');
        }
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

    public function destroy($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa banner.'], 403);
        }

        $banner = Banner::withTrashed()->findOrFail($id);

        if ($banner->image_url && Storage::disk('public')->exists($banner->image_url)) {
            Storage::disk('public')->delete($banner->image_url);
        }

        $banner->forceDelete(); // Xoá vĩnh viễn

        return response()->json(['success' => true, 'message' => 'Đã xoá vĩnh viễn banner.']);
    }

    public function softDelete($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa banner.'], 403);
        }

        $banner = Banner::findOrFail($id);
        $banner->delete();

        return response()->json(['success' => true, 'message' => 'Đã xoá mềm banner.']);
    }

    public function restore($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền khôi phục banner.'], 403);
        }

        $banner = Banner::withTrashed()->findOrFail($id);
        $banner->restore();

        return response()->json(['success' => true, 'message' => 'Khôi phục banner thành công.']);
    }

}
