<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerImage;
use Illuminate\Support\Facades\Storage;

class CustomerImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customerImages = CustomerImage::paginate(5); // Paginate the results to 8 per page

        return view('admin.custormer_images.index', compact('customerImages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.custormer_images.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, CustomerImage $customerImage)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
            'status' => 'boolean',
        ], [
            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.max' => 'Hình ảnh không được vượt quá 2MB.',
            'status.boolean' => 'Trạng thái không hợp lệ.',
        ]);

        if ($request->hasFile('image')) {
            // Xoá ảnh cũ nếu có
            if ($customerImage->image && Storage::disk('public')->exists($customerImage->image)) {
                Storage::disk('public')->delete($customerImage->image);
            }

            // Lưu ảnh mới
            $path = $request->file('image')->store('customer_images', 'public');
            $customerImage->image = $path;
        }


        CustomerImage::create([
            'image' => $path,
            'status' => $request->input('status', true),
        ]);

        return redirect()->route('customer-images.index')->with('success', 'Thêm ảnh thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customerImage = CustomerImage::findOrFail($id);

        return view('admin.custormer_images.show', compact('customerImage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $customerImage = CustomerImage::findOrFail($id);

        return view('admin.custormer_images.edit', compact('customerImage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'image' => 'nullable|image|max:2048',
            'status' => 'boolean',
        ], [
            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.max' => 'Hình ảnh không được vượt quá 2MB.',
            'status.boolean' => 'Trạng thái không hợp lệ.',
        ]);

        $customerImage = CustomerImage::findOrFail($id);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('customer_images', 'public');
            $customerImage->image = $path;
        }

        $customerImage->status = $request->input('status', true);
        $customerImage->save();

        return redirect()->route('customer-images.index')->with('success', 'Cập nhật ảnh thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customerImage = CustomerImage::findOrFail($id);

        // Xoá ảnh khỏi thư mục storage nếu tồn tại
        if ($customerImage->image && Storage::disk('public')->exists($customerImage->image)) {
            Storage::disk('public')->delete($customerImage->image);
        }

        // Xoá bản ghi trong DB
        $customerImage->delete();

        return redirect()->route('customer-images.index')->with('success', 'Xóa ảnh thành công!');
    }
}
