<?php

namespace App\Http\Controllers;

use App\Models\Volume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class VolumeController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        if ($filter === 'deleted') {
            $volumes = Volume::onlyTrashed()->orderBy('created_at', 'desc')->paginate(10);
        } elseif ($filter === 'active') {
            $volumes = Volume::whereNull('deleted_at')->orderBy('created_at', 'desc')->paginate(10);
        } else {
            $volumes = Volume::withTrashed()->orderBy('created_at', 'desc')->paginate(10);
        }
        return view('admin.volumes.index', compact('volumes', 'filter'));
    }

    public function create()
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('admin.volumes.index')->with('error', 'Bạn không có quyền thêm dung tích.');
        }
        return view('admin.volumes.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('admin.volumes.index')->with('error', 'Bạn không có quyền thêm dung tích.');
        }
        $request->validate(['name' => 'required|numeric']);
        $name = $request->input('name') . 'ml';
        Volume::create(['name' => $name]);
        return redirect()->route('admin.volumes.index')->with('success', 'Thêm dung tích thành công!');
    }

    public function edit(Volume $volume)
    {
          if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('admin.volumes.index')->with('error', 'Bạn không có quyền sửa dung tích.');
        }
        return view('admin.volumes.edit', compact('volume'));
    }

    public function update(Request $request, Volume $volume)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('admin.volumes.index')->with('error', 'Bạn không có quyền sửa dung tích.');
        }
        $request->validate(['name' => 'required|numeric']);
        $name = $request->input('name') . 'ml';
        $volume->update(['name' => $name]);

        // Lấy lại số trang từ query
        $page = $request->query('page');
        return redirect()->route('admin.volumes.index', ['page' => $page])
                         ->with('success', 'Cập nhật dung tích thành công!');
    }

    public function destroy(Request $request, Volume $volume)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa dung tích.']);
        }
        
        // Chỉ cho phép xóa mềm nếu chưa liên kết với biến thể sản phẩm
        if ($volume->productVariants()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Không thể xóa dung tích vì đang liên kết với biến thể sản phẩm.']);
        }
        
        $volume->delete();
        return response()->json(['success' => true, 'message' => 'Xóa dung tích thành công!']);
    }

    // Khôi phục dung tích đã xóa mềm
    public function restore(Request $request, $id)
    {
        $volume = Volume::withTrashed()->findOrFail($id);
        if ($volume->trashed()) {
            $volume->restore();
            return response()->json(['success' => true, 'message' => 'Khôi phục dung tích thành công!']);
        }
        return response()->json(['success' => false, 'message' => 'Dung tích này chưa bị xóa.']);
    }

    // Xóa vĩnh viễn dung tích đã xóa mềm
    public function forceDelete(Request $request, $id)
    {
        $volume = Volume::withTrashed()->findOrFail($id);
        if ($volume->productVariants()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Không thể xóa vĩnh viễn vì có sản phẩm đang sử dụng dung tích này.']);
        }
        if ($volume->trashed()) {
            $volume->forceDelete();
            return response()->json(['success' => true, 'message' => 'Xóa vĩnh viễn dung tích thành công!']);
        }
        return response()->json(['success' => false, 'message' => 'Dung tích này chưa bị xóa mềm.']);
    }
}
