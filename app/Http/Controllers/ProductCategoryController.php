<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Http\Requests\ProductCategoryRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter', 'all'); // Mặc định là 'all'

        // Chọn truy vấn theo filter
        $query = match ($filter) {
            'active' => ProductCategory::query(),         // Chỉ danh mục còn hoạt động
            'deleted' => ProductCategory::onlyTrashed(),  // Chỉ danh mục đã xoá mềm
            default => ProductCategory::withTrashed(),    // Tất cả (cả đã xoá mềm)
        };

        // Áp dụng tìm kiếm nếu có
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        // Lấy kết quả, sắp xếp và phân trang
        $categories = $query->orderBy('updated_at', 'DESC')->paginate(10);

        return view('admin.product_categories.index', compact('categories', 'search', 'filter'));
    }

    public function show($product_category)
    {
        // Lấy cả danh mục đã xóa mềm (withTrashed)
        $category = ProductCategory::withTrashed()->findOrFail($product_category);

        return view('admin.product_categories.show', compact('category'));
    }


    public function create()
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('product_categories.index')
                ->with('error', 'Bạn không có quyền thêm danh mục.');
        }

        return view('admin.product_categories.create');
    }

    public function store(ProductCategoryRequest $request)
    {
        $data = $request->validated();

        // Nếu chưa có slug, tạo slug từ name
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $originalSlug = $data['slug'];
        $counter = 1;

        // Kiểm tra slug đã tồn tại chưa, nếu có thì thêm -1, -2...
        while (ProductCategory::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        ProductCategory::create($data);

        return redirect()->route('product_categories.index')->with('success', 'Thêm danh mục thành công');
    }


    public function edit(ProductCategory $product_category)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('product_categories.index')->with('error', 'Bạn không có quyền sửa danh mục.');
        }
        return view('admin.product_categories.edit', compact('product_category'));
    }

    public function update(ProductCategoryRequest $request, ProductCategory $product_category)
    {
        $data = $request->validated();

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $originalSlug = $data['slug'];
        $counter = 1;

        // Kiểm tra slug đã tồn tại ở các bản ghi khác chưa, nếu có thì thêm -1, -2...
        while (ProductCategory::where('slug', $data['slug'])->where('id', '!=', $product_category->id)->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $product_category->update($data);

        return redirect()->route('product_categories.index')->with('success', 'Cập nhật danh mục thành công');
    }


    public function softDelete($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa danh mục.'
            ]);
        }

        $category = ProductCategory::findOrFail($id);

        $category->delete(); // ❌ chỉ xoá mềm danh mục

        // Không nên ẩn toàn bộ sản phẩm nếu ảnh hưởng đơn hàng

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
        $category->restore(); // khôi phục (cả sản phẩm liên quan)
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

        // Check nếu danh mục còn sản phẩm thì không xoá vĩnh viễn
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
