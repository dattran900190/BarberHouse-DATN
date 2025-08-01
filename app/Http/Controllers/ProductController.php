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
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('admin.products.index')->with('error', 'Bạn không có quyền thêm sản phẩm.');
        }
        if (!$request->has('variants') || count($request->variants) < 1) {
            return back()->with('error', 'Phải có ít nhất 1 biến thể')->withInput();
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
        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được thêm thành công!');
    }

    public function show(Product $product)
    {
        $product->load('category', 'variants.volume', 'images');
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
                    Storage::disk('public')->delete($image->image_url);
                    $image->delete();
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
        if ($product->trashed()) {
            // Khôi phục sản phẩm
            $product->restore();

            // Khôi phục các biến thể
            $product->variants()->onlyTrashed()->get()->each(function ($variant) {
                $variant->restore();
            });
            
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
        $variant = \App\Models\ProductVariant::withTrashed()->findOrFail($id);
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
        $variant = \App\Models\ProductVariant::findOrFail($id);
        $product = $variant->product;
        $activeVariantsCount = $product->variants()->whereNull('deleted_at')->count();

        if ($activeVariantsCount <= 1) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm phải có ít nhất 1 biến thể. Không thể xóa biến thể cuối cùng!'
                ]);
            }
            return redirect()->route('admin.products.edit', $variant->product_id)
                ->with('error', 'Sản phẩm phải có ít nhất 1 biến thể. Không thể xóa biến thể cuối cùng!');
        }

        if (!$variant->trashed()) {
            $variant->delete();
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Xóa mềm biến thể thành công!'
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
                // Xóa ảnh vật lý
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                foreach ($product->images as $image) {
                    Storage::disk('public')->delete($image->image_url);
                }
                foreach ($product->variants()->withTrashed()->get() as $variant) {
                    if ($variant->image) {
                        Storage::disk('public')->delete($variant->image);
                    }
                }

                // Xóa vĩnh viễn
                $product->forceDelete();
                
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
                        'message' => 'Đã xảy ra lỗi khi xóa sản phẩm vì sản phẩm còn liên quan tới các sản phẩm khác: '
                    ]);
                }
                return redirect()->route('admin.products.index')->with('error', 'Đã xảy ra lỗi khi xóa sản phẩm vì sản phẩm còn liên quan tới các sản phẩm khác: ');
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
    public function showTrashed($id)
    {
        $product = Product::withTrashed()
            ->where('id', $id)
            ->whereNotNull('deleted_at')
            ->with(['category', 'images', 'variants' => function ($query) {
                $query->withTrashed()->with('volume');
            }])
            ->firstOrFail();
    
        return view('admin.products.show-trashed', compact('product'));
    }
    
}