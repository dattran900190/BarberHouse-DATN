<?php

namespace App\Http\Controllers;

use App\Http\Requests\BranchRequest;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    // Hiển thị danh sách chi nhánh
    public function index(Request $request)
    {
        $search = $request->input('search');
        $branches = Branch::when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%');
        })->latest()->paginate(10);

        return view('admin.branches.index', compact('branches'));
    }

    // Hiển thị form tạo chi nhánh
    public function create()
    {
        return view('admin.branches.create');
    }

    // Lưu chi nhánh mới
    public function store(BranchRequest $request)
    {
        $data = $request->validated();
        Branch::create($data);

        return redirect()->route('admin.branches.index')->with('success', 'Thêm chi nhánh thành công');
    }

    // Hiển thị chi tiết chi nhánh
    public function show(Branch $branch)
    {
        return view('admin.branches.show', compact('branch'));
    }

    // Hiển thị form sửa
    public function edit(Branch $branch)
    {
        return view('admin.branches.edit', compact('branch'));
    }

    // Cập nhật chi nhánh
    public function update(BranchRequest $request, Branch $branch)
    {
        $branch->update($request->validated());

        return redirect()->route('admin.branches.index')->with('success', 'Cập nhật thành công');
    }

    // Xoá chi nhánh
    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('admin.branches.index')->with('success', 'Xoá chi nhánh thành công');
    }
}
