<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CustomerImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customerImages = CustomerImage::paginate(10); // Paginate the results to 8 per page

        return view('admin.custormer_images.index', compact('customerImages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('customer-images.index')->with('error', 'Bạn không có quyền truy cập.');
        }
        return view('admin.custormer_images.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Define validation rules
        $rules = [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'boolean',
        ];

        // Custom error messages
        $messages = [
            'image.required' => 'Vui lòng chọn hình ảnh.',
            'image.image' => 'Thêm ảnh với file không hợp lệ.',
            'image.mimes' => 'Sửa với file ảnh không hợp lệ. Chỉ chấp nhận jpeg, png, jpg, gif, svg.',
            'image.max' => 'Upload ảnh dung lượng quá lớn (tối đa 2MB).',
            'status.boolean' => 'Trạng thái không hợp lệ.',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle image upload
        $path = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $path = $request->file('image')->store('customer_images', 'public');
        }

        // Create new CustomerImage record
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
          if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('customer-images.index')->with('error', 'Bạn không có quyền truy cập.');
        }
        $customerImage = CustomerImage::findOrFail($id);

        return view('admin.custormer_images.edit', compact('customerImage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Define validation rules
        $rules = [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'boolean',
        ];

        // Custom error messages
        $messages = [
            'image.image' => 'Sửa ảnh với file không hợp lệ.',
            'image.mimes' => 'Sửa với file ảnh không hợp lệ. Chỉ chấp nhận jpeg, png, jpg, gif, svg.',
            'image.max' => 'Upload ảnh dung lượng quá lớn (tối đa 2MB).',
            'status.boolean' => 'Trạng thái không hợp lệ.',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Find the CustomerImage record
        $customerImage = CustomerImage::findOrFail($id);

        // Handle image upload
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Delete old image if it exists
            if ($customerImage->image && Storage::disk('public')->exists($customerImage->image)) {
                Storage::disk('public')->delete($customerImage->image);
            }
            // Store new image
            $path = $request->file('image')->store('customer_images', 'public');
            $customerImage->image = $path;
        }

        // Update status
        $customerImage->status = $request->input('status', true);
        $customerImage->save();

        return redirect()->route('customer-images.index')->with('success', 'Cập nhật ảnh thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(string $id)
{
    if (Auth::user()->role === 'admin_branch') {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập.'
            ], 403);
        }
        return redirect()->route('customer-images.index')
            ->with('error', 'Bạn không có quyền truy cập.');
    }

    $customerImage = CustomerImage::findOrFail($id);

    if ($customerImage->image && Storage::disk('public')->exists($customerImage->image)) {
        Storage::disk('public')->delete($customerImage->image);
    }

    $customerImage->delete();

    return response()->json([
        'success' => true,
        'message' => 'Xóa ảnh thành công!'
    ]);
}

}
