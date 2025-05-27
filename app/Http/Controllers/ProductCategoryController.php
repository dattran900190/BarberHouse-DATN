<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Http\Requests\ProductCategoryRequest;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categories = ProductCategory::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
        })->latest()->paginate(10);

        return view('admin.product_categories.index', compact('categories', 'search'));
    }

    public function create()
    {
        return view('admin.product_categories.create');
    }

    public function store(ProductCategoryRequest $request)
    {
        ProductCategory::create($request->validated());
        return redirect()->route('product_categories.index')->with('success', 'Thêm danh mục thành công');
    }

    public function edit(ProductCategory $product_category)
    {
        return view('admin.product_categories.edit', compact('product_category'));
    }

    public function update(ProductCategoryRequest $request, ProductCategory $product_category)
    {
        $product_category->update($request->validated());
        return redirect()->route('product_categories.index')->with('success', 'Cập nhật danh mục thành công');
    }

    public function destroy(ProductCategory $product_category)
    {
        $product_category->delete();
        return redirect()->route('product_categories.index')->with('success', 'Xóa danh mục thành công');
    }
}
