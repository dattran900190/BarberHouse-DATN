<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\CancelledAppointment;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ServiceRequest;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter', 'all'); // Mặc định là 'all' nếu không có

        // Lấy query builder tùy theo filter
        $query = match ($filter) {
            'active' => Service::query(), // chỉ service còn hoạt động
            'deleted' => Service::onlyTrashed(),
            default => Service::withTrashed(), // tất cả (kể cả đã xoá mềm)
        };

        // Thêm điều kiện search nếu có
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Sắp xếp & phân trang
        $services = $query->orderBy('created_at', 'DESC')->paginate(perPage: 10);

        return view('admin.services.index', compact('services', 'filter', 'search'));
    }


    public function create()
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('services.index')->with('error', 'Bạn không có quyền truy cập.');
        }
        return view('admin.services.create');
    }

    public function store(ServiceRequest $request)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('services.index')->with('error', 'Bạn không có quyền truy cập.');
        }
        $data = $request->validated();

        // Nếu có ảnh thì lưu vào storage/app/public/services
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }


        Service::create($data);

        return redirect()->route('services.index')->with('success', 'Thêm dịch vụ thành công');
    }

    public function show($id)
    {
        $service = Service::withTrashed()->findOrFail($id);
        return view('admin.services.show', compact('service'));
    }


    public function edit(Service $service)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('services.index')->with('error', 'Bạn không có quyền chỉnh sửa.');
        }
        return view('admin.services.edit', compact('service'));
    }

    public function update(ServiceRequest $request, Service $service)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('services.index')->with('error', 'Bạn không có quyền truy cập.');
        }
        $data = $request->validated();

        // Nếu có ảnh mới được upload
        if ($request->hasFile('image')) {
            // Xoá ảnh cũ nếu tồn tại
            if ($service->image && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }

            // Lưu ảnh mới vào thư mục services
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);

        // Lấy số trang từ request
        $currentPage = $request->input('page', 1);

        return redirect()->route('services.index', ['page' => $currentPage])
            ->with('success', 'Cập nhật thành công');
    }

    public function softDelete($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xoá danh mục.'
            ]);
        }

        $category = ProductCategory::findOrFail($id);
        $category->delete();

        // Ẩn (soft delete) tất cả sản phẩm liên kết
        Product::where('product_category_id', $category->id)->delete();

        return response()->json(['success' => true, 'message' => 'Đã xoá mềm danh mục.']);
    }

    public function restore($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền khôi phục danh mục.'
            ]);
        }

        $category = ProductCategory::withTrashed()->findOrFail($id);
        $category->restore();

        // Khôi phục tất cả sản phẩm liên quan
        Product::onlyTrashed()->where('product_category_id', $category->id)->restore();

        return response()->json(['success' => true, 'message' => 'Khôi phục danh mục thành công.']);
    }

    public function destroy($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xoá danh mục.'
            ]);
        }

        $category = ProductCategory::withTrashed()->findOrFail($id);
        $hasProducts = Product::withTrashed()
            ->where('product_category_id', $category->id)
            ->exists();

        if ($hasProducts) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xoá vĩnh viễn vì vẫn còn sản phẩm thuộc danh mục.'
            ]);
        }

        $category->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xoá vĩnh viễn danh mục.'
        ]);
    }
}
