<?php

namespace App\Http\Controllers\Admins\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSpecification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index()
    {
        return view('admins.product.index');
    }

    public function create(Request $request)
    {
        $categories = ProductCategory::active()->get();
        return view('admins.product.create', get_defined_vars());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'sku' => 'required|string|max:255|unique:products',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:product_categories,id',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'specifications_key.*' => 'nullable|string|max:255',
            'specifications_value.*' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $productData = $validated;

        // Handle product gallery images using uploadImage()
        if ($request->hasFile('images')) {
            $uploadedImages = [];
            foreach ($request->file('images') as $file) {
                $uploadedImages[] = uploadImage($file, 'images/products');
            }
            $productData['images'] = $uploadedImages;
        }

        // Handle featured image using uploadImage()
        if ($request->hasFile('featured_image')) {
            $productData['featured_image'] = uploadImage($request->file('featured_image'), 'images/products/featured');
        }

        // Remove specification arrays from validated data before create()
        $specificationKeys = $validated['specifications_key'] ?? [];
        $specificationValues = $validated['specifications_value'] ?? [];
        unset($productData['specifications_key'], $productData['specifications_value']);

        // Create product
        $product = Product::create($productData);

        // Handle specifications
        if (!empty($specificationKeys) || !empty($specificationValues)) {
            $specs = [];
            $sortOrder = 0;
            foreach ($specificationKeys as $index => $key) {
                if (!empty($key) && isset($specificationValues[$index])) {
                    $specs[] = [
                        'product_id' => $product->id,
                        'key' => $key,
                        'value' => $specificationValues[$index],
                        'sort_order' => $sortOrder++,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($specs)) {
                ProductSpecification::insert($specs);
            }
        }

        return redirect()->route('product.index')
            ->with('success', 'Product created successfully!');
    }

    public function show(Product $product)
    {
        return view('admins.product.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load('productSpecifications'); // Load specifications relationship
        $categories = ProductCategory::active()->get();
        return view('admins.product.edit', compact('product', 'categories'));
    }

    // public function update(Request $request, Product $product)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
    //         'description' => 'nullable|string',
    //         'short_description' => 'nullable|string|max:500',
    //         'price' => 'required|numeric|min:0',
    //         'sale_price' => 'nullable|numeric|min:0|lt:price',
    //         'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
    //         'stock_quantity' => 'required|integer|min:0',
    //         'category_id' => 'nullable|exists:product_categories,id',
    //         'images.*' => 'image|max:2048',
    //         'featured_image' => 'nullable|image|max:2048',
    //         'specifications_key.*' => 'nullable|string|max:255',
    //         'specifications_value.*' => 'nullable|string|max:255',
    //         'is_featured' => 'boolean',
    //         'is_active' => 'boolean',
    //         'meta_title' => 'nullable|string|max:255',
    //         'meta_description' => 'nullable|string|max:500',
    //         'remove_images' => 'nullable|array',
    //         'remove_images.*' => 'string'
    //     ]);

    //     // Handle image uploads
    //     $images = $product->images ?? [];
    //     if ($request->hasFile('images')) {
    //         foreach ($request->file('images') as $image) {
    //             $images[] = $image->store('products', 'public');
    //         }
    //     }

    //     // Handle image removal
    //     if ($request->has('remove_images')) {
    //         foreach ($request->remove_images as $imageToRemove) {
    //             // Remove from array
    //             $images = array_diff($images, [$imageToRemove]);
    //             // Delete file from storage
    //             Storage::disk('public')->delete($imageToRemove);
    //         }
    //     }

    //     $validated['images'] = array_values($images);

    //     // Handle featured image upload
    //     if ($request->hasFile('featured_image')) {
    //         // Delete old featured image if exists
    //         if ($product->featured_image) {
    //             Storage::disk('public')->delete($product->featured_image);
    //         }
    //         $validated['featured_image'] = $request->file('featured_image')->store('products/featured', 'public');
    //     }

    //     // Remove specification fields from validation data
    //     $specificationKeys = $validated['specifications_key'] ?? [];
    //     $specificationValues = $validated['specifications_value'] ?? [];
    //     unset($validated['specifications_key'], $validated['specifications_value'], $validated['remove_images']);

    //     // Update the product
    //     $product->update($validated);

    //     // Handle specifications
    //     if (!empty($specificationKeys) || !empty($specificationValues)) {
    //         // Delete existing specifications
    //         ProductSpecification::where('product_id', $product->id)->delete();

    //         // Create new specifications
    //         $specs = [];
    //         $sortOrder = 0;
    //         foreach ($specificationKeys as $index => $key) {
    //             if (!empty($key) && isset($specificationValues[$index])) {
    //                 $specs[] = [
    //                     'product_id' => $product->id,
    //                     'key' => $key,
    //                     'value' => $specificationValues[$index],
    //                     'sort_order' => $sortOrder++,
    //                     'created_at' => now(),
    //                     'updated_at' => now(),
    //                 ];
    //             }
    //         }

    //         if (!empty($specs)) {
    //             ProductSpecification::insert($specs);
    //         }
    //     }

    //     return redirect()->route('product.index')
    //         ->with('success', 'Product updated successfully!');
    // }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:product_categories,id',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'specifications_key.*' => 'nullable|string|max:255',
            'specifications_value.*' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'string',
        ]);

        $productData = $validated;

        /**
         * ✅ 1. Handle existing images
         */
        $existingImages = $product->getRawOriginal('images') ? json_decode($product->getRawOriginal('images'), true) : [];

        /**
         * ✅ 2. Handle removal of selected images
         */
        if ($request->filled('remove_images')) {
            foreach ($request->remove_images as $imageToRemove) {

                // Strip domain part from URL if present
                $relativePath = str_replace(url('/') . '/', '', $imageToRemove);

                // Filter the array
                $existingImages = array_filter($existingImages, fn($img) => $img !== $relativePath);

                // Delete the actual file
                $path = public_path($relativePath);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }

        /**
         * ✅ 3. Handle new image uploads using uploadImage()
         */
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $existingImages[] = uploadImage($file, 'images/products');
            }
        }

        $productData['images'] = array_values($existingImages);

        /**
         * ✅ 4. Handle featured image upload using uploadImage()
         */
        if ($request->hasFile('featured_image')) {
            // delete old featured image if exists
            if ($product->featured_image && file_exists(public_path($product->getRawOriginal('featured_image')))) {
                unlink(public_path($product->getRawOriginal('featured_image')));
            }

            $productData['featured_image'] = uploadImage($request->file('featured_image'), 'images/products/featured');
        }

        /**
         * ✅ 5. Handle specifications
         */
        $specificationKeys = $validated['specifications_key'] ?? [];
        $specificationValues = $validated['specifications_value'] ?? [];
        unset($productData['specifications_key'], $productData['specifications_value'], $productData['remove_images']);

        $product->update($productData);

        // Replace specifications
        ProductSpecification::where('product_id', $product->id)->delete();
        $specs = [];
        $sortOrder = 0;
        foreach ($specificationKeys as $index => $key) {
            if (!empty($key) && isset($specificationValues[$index])) {
                $specs[] = [
                    'product_id' => $product->id,
                    'key' => $key,
                    'value' => $specificationValues[$index],
                    'sort_order' => $sortOrder++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        if (!empty($specs)) {
            ProductSpecification::insert($specs);
        }

        return redirect()->route('product.index')
            ->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('product.index')
            ->with('success', 'Product deleted successfully!');
    }

    public function getProducts(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with(['category', 'reviews'])->orderBy('id', 'DESC');

            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('checkbox', function($row) {
                    return '<input type="checkbox" name="product_id[]" value="'.$row->id.'">';
                })
                ->editColumn('name', function($row) {
                    $html = '<div >';
                    
                    $html .= '<strong>'.$row->name.'</strong><br>';
                    $html .= '<div class="text-muted py-1">SKU: '.$row->sku.'</div>';
                    if ($row->is_featured) {
                        $html .= ' <span class="badge bg-warning text-dark">Featured</span>';
                    }
                    $html .= '</div></div>';
                    return $html;
                })
                ->editColumn('price', function($row) {
                    $html = '$'.number_format($row->price, 2);
                    if ($row->sale_price) {
                        $html = '<span class="text-decoration-line-through text-muted">$'.number_format($row->price, 2).'</span><br>';
                        $html .= '<strong class="text-danger">$'.number_format($row->sale_price, 2).'</strong>';
                    }
                    return $html;
                })
                ->addColumn('stock_status', function($row) {
                    if ($row->stock_quantity <= 0) {
                        return '<span class="badge bg-danger">Out of Stock</span>';
                    } elseif ($row->stock_quantity <= 10) {
                        return '<span class="badge bg-warning">Low Stock ('.$row->stock_quantity.')</span>';
                    } else {
                        return '<span class="badge bg-success">In Stock ('.$row->stock_quantity.')</span>';
                    }
                })
                ->addColumn('category', function($row) {
                    return $row->category ? $row->category->name : 'Uncategorized';
                })
                ->addColumn('rating', function($row) {
                    $avgRating = round($row->average_rating, 1);
                    $reviewsCount = $row->reviews_count;
                    if ($reviewsCount > 0) {
                        return '<div class="text-warning">'.str_repeat('★', floor($avgRating)).str_repeat('☆', 5-floor($avgRating)).'</div><small>('.$avgRating.' / '.$reviewsCount.' reviews)</small>';
                    }
                    return '<small class="text-muted">No reviews</small>';
                })
                ->editColumn('is_active', function($row) {
                    return $row->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('actions', function($row) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="'.route('product.show', $row->id).'">
                                    <i class="ti-eye"></i> View
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="viewReviews('.$row->id.', \''.addslashes($row->name).'\')">
                                    <i class="ti-comments"></i> View Reviews
                                </a></li>
                                <li><a class="dropdown-item" href="'.route('product.edit', $row->id).'">
                                    <i class="ti-pencil"></i> Edit
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="updateStock('.$row->id.')">
                                    <i class="ti-package"></i> Update Stock
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="'.route('product.destroy', $row->id).'" method="POST" class="d-inline">
                                        '.csrf_field().'
                                        '.method_field('DELETE').'
                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm(\'Are you sure?\')">
                                            <i class="ti-trash"></i> Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>';
                })
                ->addColumn('id', function($row) {
                    return $row->id;
                })
                ->addColumn('stock_quantity', function($row) {
                    return $row->stock_quantity;
                })
                ->rawColumns(['checkbox', 'name', 'price', 'stock_status', 'rating', 'is_active', 'actions'])
                ->make(true);
        }
    }

    /**
     * Get the proper image URL for display
     */
    private function getImageUrl($imagePath)
    {
        // If it's already a full URL, return it
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }

        // Otherwise, it's a relative path, so generate the full URL
        return asset('storage/'.$imagePath);
    }

    public function updateStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'stock_quantity' => 'required|integer|min:0'
        ]);

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully!'
        ]);
    }

    public function getProductReviews(Product $product)
    {
        $reviews = $product->reviews()
            ->with('user:id,name,profile_photo_url')
            ->orderByDesc('created_at')
            ->get(['id', 'user_id', 'rating', 'title', 'comment', 'is_verified', 'created_at']);

        return response()->json([
            'product' => $product->name,
            'reviews' => $reviews
        ]);
    }
}
