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
        $categories = ProductCategory::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%");
        })->latest()->paginate(5);

        return view('admin.product_categories.index', compact('categories', 'search'));
    }
    public function show(ProductCategory $product_category)
    {
        return view('admin.product_categories.show', compact('product_category'));
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


    public function destroy(ProductCategory $product_category)
    {
         if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('product_categories.index')->with('error', 'Bạn không có quyền xóa danh mục.');
        }
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('product_categories.index')
                ->with('error', 'Bạn không có quyền xóa danh mục.');
        }
        // Kiểm tra nếu có sản phẩm liên quan
        if ($product_category->products()->exists()) {
            return redirect()->route('product_categories.index')
                ->with('error', 'Không thể xóa danh mục vì còn sản phẩm liên quan!!');
        }

        // Nếu không có thì mới được xóa
        $product_category->delete();

        return redirect()->route('product_categories.index')
            ->with('success', 'Xóa danh mục thành công');
    }
}
