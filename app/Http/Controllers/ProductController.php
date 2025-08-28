<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Volume;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Events\ProductUpdated;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->query('filter', 'all');

        $query = Product::with('category', 'variants.volume', 'images');

        // if ($filter === 'deleted') {
        //     $query->onlyTrashed();
        // } elseif ($filter === 'active') {
        //     // Mặc định là chỉ lấy những product chưa bị xóa mềm, không cần thêm điều kiện
        // } else { // 'all'
        //     $query->withTrashed();
        // }

        $query = match ($filter) {
            'active' => Product::query(), // chỉ product còn hoạt động
            'deleted' => Product::onlyTrashed(),
            default => Product::withTrashed(), // tất cả (kể cả đã xoá mềm)
        };

        $products = $query->when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%');
        })
            ->orderBy('created_at', 'DESC')
            ->paginate(perPage: 10);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('admin.products.index')->with('error', 'Bạn không có quyền thêm sản phẩm.');
        }
        $categories = ProductCategory::all();
        $volumes = Volume::all();
        return view('admin.products.create', compact('categories', 'volumes'));
    }

    public function store(ProductRequest $request)
    {
        $request->validate([

            'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
            'additional_images' => 'required|array|min:1',
            'additional_images.*' => 'image|mimes:jpg,jpeg,png,gif|max:2048',
        ], [
            'image.required' => 'Vui lòng chọn ảnh sản phẩm.',
            'additional_images.required' => 'Vui lòng chọn ít nhất 1 ảnh bổ sung.',
            'additional_images.*.image' => 'Ảnh bổ sung phải là định dạng hình ảnh.',
            'additional_images.*.mimes' => 'Ảnh bổ sung phải có định dạng: jpg, jpeg, png, gif.',
            'additional_images.*.max' => 'Kích thước ảnh bổ sung không được vượt quá 2MB.',
        ]);

        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('admin.products.index')->with('error', 'Bạn không có quyền thêm sản phẩm.');
        }
        if (!$request->has('variants') || count($request->variants) < 1) {
            return back()->with('error', 'Phải có ít nhất 1 biến thể')->withInput();
        }
        // Validate không trùng dung tích
        if ($request->has('variants')) {
            $volumes = array_column($request->variants, 'volume_id');
            if (count($volumes) !== count(array_unique($volumes))) {
                return back()
                    ->with('error', 'Không được chọn trùng dung tích cho các biến thể!')
                    ->withInput();
            }
        }

        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'product_category_id' => $request->product_category_id,
            'name' => $request->name,
            'description' => $request->description,
            'long_description' => $request->long_description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
        ]);

        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $image) {
                if ($image->isValid()) {
                    $additionalImagePath = $image->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $additionalImagePath,
                    ]);
                }
            }
        }

        if ($request->has('variants')) {
            foreach ($request->variants as $index => $variant) {
                $variantImagePath = $imagePath;
                if ($request->hasFile("variants.$index.image") && $request->file("variants.$index.image")->isValid()) {
                    $variantImagePath = $request->file("variants.$index.image")->store('products', 'public');
                }
                ProductVariant::create([
                    'product_id' => $product->id,
                    'volume_id' => $variant['volume_id'],
                    'name' => $variant['name'] ?? $product->name,
                    'price' => $variant['price'],
                    'stock' => $variant['stock'],
                    'image' => $variantImagePath,
                ]);
            }
        }
        $totalStock = ProductVariant::where('product_id', $product->id)->sum('stock');
        $product->update(['stock' => $totalStock]);
        event(new ProductUpdated());
        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được thêm thành công!');
    }

    public function show($id)
    {
        $product = Product::withTrashed()
            ->with(['category', 'images', 'variants' => function ($query) {
                $query->withTrashed()->with('volume');
            }])
            ->findOrFail($id);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('admin.products.index')->with('error', 'Bạn không có quyền sửa sản phẩm.');
        }
        $categories = ProductCategory::all();
        $volumes = Volume::all();
        // Nạp lại product từ database để đảm bảo variants mới nhất
        $product = Product::with(['variants', 'images'])->findOrFail($product->id);
        return view('admin.products.edit', compact('product', 'categories', 'volumes'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('admin.products.index')->with('error', 'Bạn không có quyền sửa sản phẩm.');
        }
        // Validate không trùng dung tích
        if ($request->has('variants')) {
            $volumes = array_column($request->variants, 'volume_id');
            if (count($volumes) !== count(array_unique($volumes))) {
                return back()
                    ->with('error', 'Không được chọn trùng dung tích cho các biến thể!')
                    ->withInput();
            }
        }
        DB::transaction(function () use ($request, $product) {
            // 1. Xử lý ảnh chính sản phẩm
            $imagePath = $product->image;
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('image')->store('products', 'public');
            }

            // 2. Cập nhật thông tin sản phẩm
            $product->update([
                'product_category_id' => $request->product_category_id,
                'name' => $request->name,
                'description' => $request->description,
                'long_description' => $request->long_description,
                'price' => $request->price,
                'stock' => $request->stock,
                'image' => $imagePath,
            ]);

            // 3. Xử lý ảnh bổ sung
            if ($request->has('delete_images')) {
                $imagesToDelete = ProductImage::whereIn('id', $request->delete_images)->get();
                foreach ($imagesToDelete as $image) {
                    if ($image->image_url && Storage::disk('public')->exists($image->image_url)) {
                        Storage::disk('public')->delete($image->image_url);
                    }
                    $image->forceDelete();
                }
            }

            if ($request->hasFile('additional_images')) {
                foreach ($request->file('additional_images') as $image) {
                    if ($image->isValid()) {
                        $additionalImagePath = $image->store('products', 'public');
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_url' => $additionalImagePath,
                        ]);
                    }
                }
            }

            // 4. Xử lý biến thể
            // Xóa các biến thể được chọn qua checkbox delete_variants
            if ($request->has('delete_variants')) {
                $variantsToDelete = ProductVariant::whereIn('id', $request->delete_variants)->get();
                foreach ($variantsToDelete as $variant) {
                    if ($variant->image) {
                        Storage::disk('public')->delete($variant->image);
                    }
                    $variant->delete();
                }
            }

            // 5. Cập nhật hoặc tạo mới biến thể
            foreach ($request->variants ?? [] as $index => $variantData) {
                // Nếu biến thể này nằm trong danh sách xóa, bỏ qua
                if (!empty($variantData['id']) && $request->has('delete_variants') && in_array($variantData['id'], $request->delete_variants)) {
                    continue;
                }
                // Xử lý ảnh biến thể
                $variantImagePath = isset($variantData['id']) ? ProductVariant::find($variantData['id'])?->image : null;

                if ($request->hasFile("variants.$index.image") && $request->file("variants.$index.image")->isValid()) {
                    if ($variantImagePath) {
                        Storage::disk('public')->delete($variantImagePath);
                    }
                    $variantImagePath = $request->file("variants.$index.image")->store('products', 'public');
                }

                if (!empty($variantData['id']) && ProductVariant::find($variantData['id'])) {
                    // Cập nhật biến thể hiện có
                    ProductVariant::where('id', $variantData['id'])->update([
                        'volume_id' => $variantData['volume_id'],
                        'name' => $variantData['name'] ?? $product->name,
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'],
                        'image' => $variantImagePath,
                    ]);
                } else {
                    // Tạo biến thể mới
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'volume_id' => $variantData['volume_id'],
                        'name' => $variantData['name'] ?? $product->name,
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'],
                        'image' => $variantImagePath,
                    ]);
                }
            }

            // 6. Cập nhật tổng tồn kho sản phẩm dựa trên các biến thể còn lại
            $totalStock = ProductVariant::where('product_id', $product->id)->sum('stock');
            $product->update(['stock' => $totalStock]);
        });
        event(new ProductUpdated());
        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được cập nhật thành công!');
    }

    public function destroy(Product $product)
    {
        if (Auth::user()->role === 'admin_branch') {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa sản phẩm.'
                ]);
            }
            return redirect()->route('admin.products.index')->with('error', 'Bạn không có quyền xóa sản phẩm.');
        }

        // Xóa mềm các biến thể trước
        foreach ($product->variants as $variant) {
            $variant->delete();
        }

        // Xóa mềm sản phẩm
        $product->delete();
        event(new ProductUpdated());

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sản phẩm đã được xóa mềm thành công!'
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được xóa mềm thành công!');
    }

    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        if (Auth::user()->role === 'admin_branch') {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa sản phẩm.'
                ]);
            }
            return redirect()->route('admin.products.index')->with('error', 'Bạn không có quyền xóa sản phẩm.');
        }
        if ($product->trashed()) {
            // Khôi phục sản phẩm
            $product->restore();

            // Khôi phục các biến thể
            $product->variants()->onlyTrashed()->get()->each(function ($variant) {
                $variant->restore();
            });
            $product->images()->onlyTrashed()->get()->each(function ($image) {
                $image->restore();
            });
            event(new ProductUpdated());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sản phẩm đã được khôi phục thành công!'
                ]);
            }
            return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được khôi phục thành công!');
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không ở trong thùng rác.'
            ]);
        }
        return redirect()->route('admin.products.index')->with('error', 'Sản phẩm không ở trong thùng rác.');
    }

    public function restoreVariant($id)
    {
        $variant = ProductVariant::withTrashed()->findOrFail($id);
        if ($variant->trashed()) {
            $variant->restore();
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kích hoạt lại biến thể thành công!'
                ]);
            }
            return redirect()->route('admin.products.edit', $variant->product_id)->with('success', 'Khôi phục biến thể thành công!');
        }
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Biến thể này chưa bị xóa mềm.'
            ]);
        }
        return redirect()->back()->with('error', 'Biến thể này chưa bị xóa mềm.');
    }

    public function softDeleteVariant($id)
    {
        $variant = ProductVariant::findOrFail($id);
        $product = $variant->product;
        $activeVariantsCount = $product->variants()->whereNull('deleted_at')->count();

        // if ($activeVariantsCount <= 1) {
        //     if (request()->expectsJson()) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Sản phẩm phải có ít nhất 1 biến thể. Không thể xóa biến thể cuối cùng!'
        //         ]);
        //     }
        //     return redirect()->route('admin.products.edit', $variant->product_id)
        //         ->with('error', 'Sản phẩm phải có ít nhất 1 biến thể. Không thể xóa biến thể cuối cùng!');
        // }

        if (!$variant->trashed()) {
            $variant->delete();
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ẩn biến thể thành công!'
                ]);
            }
            return redirect()->route('admin.products.edit', $variant->product_id)->with('success', 'Xóa mềm biến thể thành công!');
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Biến thể này đã bị xóa mềm.'
            ]);
        }
        return redirect()->back()->with('error', 'Biến thể này đã bị xóa mềm.');
    }

    public function forceDelete($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        if (Auth::user()->role === 'admin_branch') {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa sản phẩm.'
                ]);
            }
            return redirect()->route('admin.products.index')->with('error', 'Bạn không có quyền xóa sản phẩm.');
        }

        if ($product->trashed()) {
            // Kiểm tra xem sản phẩm có trong bất kỳ đơn hàng nào không
            $isInOrder = DB::table('order_items')
                ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
                ->where('product_variants.product_id', $product->id)
                ->exists();

            if ($isInOrder) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không thể xóa vĩnh viễn sản phẩm vì đã tồn tại trong đơn hàng.'
                    ]);
                }
                return redirect()->route('admin.products.index')->with('error', 'Không thể xóa vĩnh viễn sản phẩm vì đã tồn tại trong đơn hàng.');
            }

            // Nếu không có trong đơn hàng, tiến hành xóa
            try {
                $product->forceDelete();
                // Xóa ảnh vật lý
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                foreach ($product->images as $image) {
                    Storage::disk('public')->delete($image->image_url);
                    $image->forceDelete();
                }
                foreach ($product->variants()->withTrashed()->get() as $variant) {
                    if ($variant->image) {
                        Storage::disk('public')->delete($variant->image);
                    }
                    $variant->forceDelete();
                }

                // Xóa vĩnh viễn
                // $product->forceDelete();

                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Sản phẩm đã được xóa vĩnh viễn!'
                    ]);
                }
                return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được xóa vĩnh viễn!');
            } catch (\Exception $e) {
                // Phòng trường hợp có lỗi khác
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Đã xảy ra lỗi khi xóa sản phẩm vì sản phẩm còn liên quan tới biến thể của sản phẩm này: '
                    ]);
                }
                return redirect()->route('admin.products.index')->with('error', 'Đã xảy ra lỗi khi xóa sản phẩm vì sản phẩm còn liên quan tới biến thể của sản phẩm này:');
            }
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm cần được xóa mềm trước.'
            ]);
        }
        return redirect()->route('admin.products.index')->with('error', 'Sản phẩm cần được xóa mềm trước.');
    }

    public function hardDeleteVariant($id)
    {
        $variant = ProductVariant::withTrashed()->findOrFail($id);

        // Kiểm tra quyền
        if (Auth::user()->role === 'admin_branch') {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa biến thể.'
                ]);
            }
            return redirect()->back()->with('error', 'Bạn không có quyền xóa biến thể.');
        }

        // Kiểm tra xem biến thể có trong bất kỳ đơn hàng nào không
        $isInOrder = DB::table('order_items')
            ->where('product_variant_id', $variant->id)
            ->exists();

        if ($isInOrder) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa cứng biến thể vì đã tồn tại trong đơn hàng.'
                ]);
            }
            return redirect()->back()->with('error', 'Không thể xóa cứng biến thể vì đã tồn tại trong đơn hàng.');
        }

        // Kiểm tra xem biến thể đã bị xóa mềm chưa
        if (!$variant->trashed()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Biến thể cần được xóa mềm trước khi xóa cứng.'
                ]);
            }
            return redirect()->back()->with('error', 'Biến thể cần được xóa mềm trước khi xóa cứng.');
        }

        try {
            // Xóa ảnh vật lý của biến thể
            if ($variant->image) {
                Storage::disk('public')->delete($variant->image);
            }

            // Xóa vĩnh viễn biến thể
            $variant->forceDelete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Biến thể đã được xóa cứng thành công!'
                ]);
            }
            return redirect()->back()->with('success', 'Biến thể đã được xóa cứng thành công!');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tạm thời bạn chưa thể xóa vĩnh viễn biến thể này vì sản phẩm đã tồn tại trong giỏ hàng khách hàng.'
                ]);
            }
            return redirect()->back()->with('error', 'Tạm thời bạn chưa thể xóa vĩnh viễn biến thể này vì sản phẩm đã tồn tại trong giỏ hàng khách hàng.');
        }
    }
}
