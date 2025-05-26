<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Volume;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $products = Product::with('category', 'variants.volume', 'images')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = ProductCategory::all();
        $volumes = Volume::all();
        return view('admin.products.create', compact('categories', 'volumes'));
    }

    public function store(ProductRequest $request)
    {
        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'product_category_id' => $request->product_category_id,
            'name' => $request->name,
            'description' => $request->description,
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

        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được thêm thành công!');
    }

    public function show(Product $product)
    {
        $product->load('category', 'variants.volume', 'images');
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::all();
        $volumes = Volume::all();
        $product->load('variants', 'images');
        return view('admin.products.edit', compact('product', 'categories', 'volumes'));
    }

    public function update(ProductRequest $request, Product $product)
    {
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
        });

        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được cập nhật thành công!');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_url);
        }
        $product->images()->delete();

        foreach ($product->variants as $variant) {
            if ($variant->image) {
                Storage::disk('public')->delete($variant->image);
            }
        }
        $product->variants()->delete();

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được xóa thành công!');
    }
}