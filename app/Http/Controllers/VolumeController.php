<?php

namespace App\Http\Controllers;

use App\Models\Volume;
use Illuminate\Http\Request;

class VolumeController extends Controller
{
    public function index()
    {
        $volumes = Volume::paginate(10); 
        return view('admin.volumes.index', compact('volumes'));
    }

    public function create()
    {
        return view('admin.volumes.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        Volume::create($request->all());
        return redirect()->route('admin.volumes.index')->with('success', 'Thêm dung tích thành công!');
    }

    public function edit(Volume $volume)
    {
        return view('admin.volumes.edit', compact('volume'));
    }

    public function update(Request $request, Volume $volume)
    {
        $request->validate(['name' => 'required']);
        $volume->update($request->all());

        // Lấy lại số trang từ query
        $page = $request->query('page');
        return redirect()->route('admin.volumes.index', ['page' => $page])
                         ->with('success', 'Cập nhật dung tích thành công!');
    }

    public function destroy(Request $request, Volume $volume)
    {
        // Kiểm tra nếu có sản phẩm đang dùng volume này
        if ($volume->productVariants()->count() > 0) {
            return redirect()->back()->with('error', 'Không thể xóa vì có sản phẩm đang sử dụng dung tích này.');
        }

        $volume->delete();

        $page = $request->query('page');
        return redirect()->route('admin.volumes.index', ['page' => $page])
                         ->with('success', 'Xóa dung tích thành công!');
    }
}
