<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromotionRequest;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter', 'all');

        $query = match ($filter) {
            '1' => Promotion::query(),
            '0' => Promotion::onlyTrashed(),
            default => Promotion::withTrashed(),
        };

        if ($search) {
            $query->where('code', 'like', '%' . $search . '%');
        }

        $promotions = $query->latest()->paginate(10);

        return view('admin.promotions.index', compact('promotions', 'search', 'filter'));
    }

    public function create()
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('promotions.index')->with('error', 'Bạn không có quyền tạo mã giảm giá.');
        }

        return view('admin.promotions.create');
    }

    public function store(PromotionRequest $request)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('promotions.index')->with('error', 'Bạn không có quyền thêm mã giảm giá.');
        }

        $data = $request->validated();
        $data['is_active'] = 1;
        Promotion::create($data);

        return redirect()->route('promotions.index')->with('success', 'Mã giảm giá đã được tạo.');
    }

    public function edit(Promotion $promotion)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('promotions.index')->with('error', 'Bạn không có quyền chỉnh sửa mã giảm giá.');
        }

        return view('admin.promotions.edit', compact('promotion'));
    }

    public function update(PromotionRequest $request, Promotion $promotion)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('promotions.index')->with('error', 'Bạn không có quyền cập nhật mã giảm giá.');
        }

        $currentPage = $request->input('page', 1);
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', $promotion->is_active);


        $promotion->update($data);

        return redirect()->route('promotions.index', ['page' => $currentPage])->with('success', 'Mã giảm giá đã được cập nhật.');
    }

    public function show($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('promotions.index')->with('error', 'Bạn không có quyền xem chi tiết mã giảm giá.');
        }
        $promotion = Promotion::withTrashed()->findOrFail($id);
        return view('admin.promotions.show', compact('promotion'));
    }

    /**
     * Xóa vĩnh viễn mã giảm giá (chỉ nếu đã soft delete trước đó)
     */
    public function destroy($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa mã giảm giá.'
            ]);
        }

        $promotion = Promotion::withTrashed()->findOrFail($id);

        // Nếu mã chưa bị xóa mềm thì không cho xóa cứng luôn
        if (!$promotion->trashed()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần xóa mềm trước khi xoá vĩnh viễn.'
            ]);
        }

        try {
            $promotion->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xoá vĩnh viễn mã giảm giá.'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Kiểm tra mã lỗi SQL nếu muốn chính xác hơn (1451 là "Cannot delete or update a parent row...")
            if ($e->getCode() == 23000) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã giảm giá đang được sử dụng và không thể xóa.'
                ]);
            }

            // Trường hợp lỗi khác
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi xóa: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Xoá mềm mã giảm giá
     */
    public function softDelete($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa mềm mã giảm giá.'
            ]);
        }

        $promotion = Promotion::findOrFail($id);

        // 👉 Tắt kích hoạt trước khi xóa mềm
        $promotion->update(['is_active' => 0]);

        $promotion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa mềm mã giảm giá.'
        ]);
    }


    /**
     * Khôi phục mã giảm giá đã bị xoá mềm
     */
    public function restore($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền khôi phục mã giảm giá.'
            ]);
        }

        $promotion = Promotion::withTrashed()->findOrFail($id);
        $promotion->restore();
        $promotion->update(['is_active' => 1]);
        return response()->json([
            'success' => true,
            'message' => 'Khôi phục mã giảm giá thành công.'
        ]);
    }
}
