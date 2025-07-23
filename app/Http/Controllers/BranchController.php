<?php

namespace App\Http\Controllers;

use App\Http\Requests\BranchRequest;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    // Hiển thị danh sách chi nhánh
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter', 'all');

        $user = Auth::user();
        $query = Branch::query();

        if ($user->role === 'admin_branch') {
            $query->where('id', $user->branch_id); // Giả sử có cột `branch_id` trong bảng `users`
        } else {
            // Nếu là admin -> xử lý filter như cũ
            if ($filter === 'deleted') {
                $query->onlyTrashed();
            } elseif ($filter === 'all') {
                $query->withTrashed();
            }
        }

        // Nếu muốn chỉ hiện chi nhánh hoạt động khi không chọn gì, thì bỏ else if trên và để mặc định

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $branches = $query->latest()->paginate(10);

        return view('admin.branches.index', compact('branches'));
    }

    // Hiển thị form tạo chi nhánh
    public function create()
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('branches.index')->with('error', 'Bạn không có quyền thêm chi nhánh.');
        }
        return view('admin.branches.create');
    }

    // Lưu chi nhánh mới
    public function store(BranchRequest $request)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('branches.index')->with('error', 'Bạn không có quyền thêm chi nhánh.');
        }
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('branches', 'public');
        }

        Branch::create($data);

        return redirect()->route('branches.index')->with('success', 'Thêm chi nhánh thành công!');
    }

    // Hiển thị chi tiết chi nhánh
    public function show(Request $request, Branch $branch)
    {
        $search = $request->input('search');

        // Lấy thợ thuộc chi nhánh này, có tìm kiếm tên thợ và phân trang
        $barbers = $branch->barbers()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(10);

        return view('admin.branches.show', compact('branch', 'barbers', 'search'));
    }



    // Hiển thị form sửa
    public function edit(Branch $branch)
    {
        
        return view('admin.branches.edit', compact('branch'));
    }

    // Cập nhật chi nhánh
    public function update(BranchRequest $request, Branch $branch)
    {
        // Lấy dữ liệu đã validate
        $data = $request->validated();

        // Nếu có upload ảnh mới
        if ($request->hasFile('image')) {
            // Xoá ảnh cũ nếu tồn tại
            if ($branch->image && file_exists(public_path('storage/' . $branch->image))) {
                unlink(public_path('storage/' . $branch->image));
            }

            // Lưu ảnh mới vào thư mục public/storage/branches
            $imagePath = $request->file('image')->store('branches', 'public');
            $data['image'] = $imagePath;
        }

        // Cập nhật dữ liệu chi nhánh
        $branch->update($data);

        // Trở lại trang trước (có phân trang nếu có)
        $currentPage = $request->input('page', 1);

        return redirect()->route('branches.index', ['page' => $currentPage])
            ->with('success', 'Cập nhật thành công');
    }



    // Xoá chi nhánh
    public function destroy(Branch $branch)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('branches.index')->with('error', 'Bạn không có quyền xóa chi nhánh.');
        }
        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Xoá chi nhánh thành công');
    }
    public function softDelete($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa chi nhánh'
            ]);
        }
        $branch = Branch::findOrFail($id);
        $branch->delete();
        return response()->json(['success' => true, 'message' => 'Đã xoá mềm chi nhánh!']);
    }

    public function restore($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền khôi phục chi nhánh'
            ]);
        }
        $branch = Branch::withTrashed()->findOrFail($id);
        $branch->restore();
        return response()->json(['success' => true, 'message' => 'Đã khôi phục chi nhánh!']);
    }

    public function forceDelete($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa chi nhánh'
            ]);
        }
        $branch = Branch::withTrashed()->findOrFail($id);
        $branch->forceDelete();
        return response()->json(['success' => true, 'message' => 'Đã xoá vĩnh viễn chi nhánh!']);
    }
}
