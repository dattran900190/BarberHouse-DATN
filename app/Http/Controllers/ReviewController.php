<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter', 'all');

        $query = match ($filter) {
            'deleted' => Review::onlyTrashed()->with(['user:id,name', 'barber:id,name']),
            'active' => Review::query()->with(['user:id,name', 'barber:id,name']),
            default => Review::withTrashed()->with(['user:id,name', 'barber:id,name']),
        };

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q1) use ($search) {
                    $q1->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('barber', function ($q2) use ($search) {
                    $q2->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $reviews = $query->orderBy('updated_at', 'DESC')->paginate(5);

        return view('admin.reviews.index', compact('reviews', 'search', 'filter'));
    }

    public function create()
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('reviews.index')->with('error', 'Bạn không có quyền thêm bình luận.');
        }
        return view('admin.reviews.create');
    }

    public function store(ReviewRequest $request)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('reviews.index')->with('error', 'Bạn không có quyền thêm bình luận.');
        }

        Review::create($request->validated());

        return redirect()->route('reviews.index')->with('success', 'Thêm bình luận thành công');
    }

    public function show($id)
    {
        $review = Review::withTrashed()->findOrFail($id);
        return view('admin.reviews.show', compact('review'));
    }

    public function edit(Review $review)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('reviews.index')->with('error', 'Bạn không có quyền sửa bình luận.');
        }

        // Không cho phép sửa nếu bản ghi đã bị xóa mềm
        if ($review->trashed()) {
            return redirect()->route('reviews.index')->with('error', 'Không thể sửa bình luận đã bị xóa.');
        }
        return view('admin.reviews.edit', compact('review'));
    }

    public function update(ReviewRequest $request, Review $review)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('reviews.index')->with('error', 'Bạn không có quyền sửa bình luận.');
        }

        $review->update($request->validated());

        $currentPage = $request->input('page', 1);

        return redirect()->route('reviews.index', ['page' => $currentPage])
            ->with('success', 'Cập nhật thành công');
    }

    // Soft delete
    public function softDelete($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa bình luận.'
            ]);
        }

        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json(['success' => true, 'message' => 'Đã xoá mềm bình luận.']);
    }

    // Khôi phục
    public function restore($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền khôi phục bình luận.'
            ]);
        }

        $review = Review::withTrashed()->findOrFail($id);
        $review->restore();

        return response()->json(['success' => true, 'message' => 'Khôi phục bình luận thành công.']);
    }

    public function destroy($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa bình luận.'
            ]);
        }

        $review = Review::withTrashed()->findOrFail($id);
        $review->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xoá vĩnh viễn bình luận.'
        ]);
    }
}
