<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromotionRequest;
use App\Models\Promotion;
use Illuminate\Http\Request;

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
        return view('admin.promotions.create');
    }

    public function store(PromotionRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;

        Promotion::create($data);

        return redirect()->route('promotions.index')->with('success', 'Mã giảm giá đã được tạo.');
    }

    public function edit(Promotion $promotion)
    {
        return view('admin.promotions.edit', compact('promotion'));
    }

    public function update(PromotionRequest $request, Promotion $promotion)
    {
        $currentPage = $request->input('page', 1);
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;

        $promotion->update($data);

        return redirect()->route('promotions.index', ['page' => $currentPage])->with('success', 'Mã giảm giá đã được cập nhật.');
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();
        return redirect()->route('promotions.index')->with('success', 'Mã giảm giá đã được xóa.');
    }
}