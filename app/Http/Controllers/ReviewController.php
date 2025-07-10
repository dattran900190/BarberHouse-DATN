<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(request $request)
    {
        $search = $request->input('search');
        $reviews = Review::with(['user:id,name', 'barber:id,name'])
            ->when($search, function ($query, $search) {
                // tìm theo tên user và tên barber
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('barber', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('id', 'DESC')
            ->paginate(5);

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('reviews.index')->with('error', 'Bạn không có quyền thêm bình luận.');
        }
        return view('admin.reviews.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReviewRequest $request)
    {
          if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('reviews.index')->with('error', 'Bạn không có quyền thêm bình luận.');
        }
        $data = $request->validated();

        Review::create($data);

        return redirect()->route('reviews.index')->with('success', 'Thêm bình luận thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
         if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('reviews.index')->with('error', 'Bạn không có quyền sửa bình luận.');
        }
        return view('admin.reviews.edit', compact('review'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReviewRequest $request, Review $review)
    {
          if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('reviews.index')->with('error', 'Bạn không có quyền sửa bình luận.');
        }
        $data = $request->validated();

        $review->update($data);

        // Lấy số trang từ request
        $currentPage = $request->input('page', 1);

        return redirect()->route('reviews.index', ['page' => $currentPage])
            ->with('success', 'Cập nhật thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
         if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('reviews.index')->with('error', 'Bạn không có quyền xóa bình luận.');
        }
        $review->delete();

        return redirect()->route('reviews.index')->with('success', 'Xoá bình luận thành công');
    }
}
