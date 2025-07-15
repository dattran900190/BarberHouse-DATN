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
        $query = Promotion::query();

        if ($request->has('search') && !empty($request->search)) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status')) {
            $query->where('is_active', $request->status);
        }

        $promotions = $query->latest()->paginate(10);

        return view('admin.promotions.index', compact('promotions'));
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
        $data['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;

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
        $data['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;

        $promotion->update($data);

        return redirect()->route('promotions.index', ['page' => $currentPage])->with('success', 'Mã giảm giá đã được cập nhật.');
    }

    // chi tiết mã giảm giá
    public function show(Promotion $promotion)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('promotions.index')->with('error', 'Bạn không có quyền xem chi tiết mã giảm giá.');
        }

        return view('admin.promotions.show', compact('promotion'));
    }
    public function destroy(Promotion $promotion)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('promotions.index')->with('error', 'Bạn không có quyền xóa mã giảm giá.');
        }

        $promotion->delete();
        return redirect()->route('promotions.index')->with('success', 'Mã giảm giá đã được xóa.');
    }
}
