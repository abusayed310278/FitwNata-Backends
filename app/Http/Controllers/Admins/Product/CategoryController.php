<?php

namespace App\Http\Controllers\Admins\Product;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admins.product.categories.index');
    }

    public function create()
    {
        $categories = ProductCategory::root()->get();
        return view('admins.product.categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:product_categories',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:product_categories,id',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = uploadFile($request->file('image'),'categories');
        }

        ProductCategory::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully!');
    }

    public function show(ProductCategory $category)
    {
        $category->load(['products', 'children', 'parent']);
        return view('admins.product.categories.show', compact('category'));
    }

    public function edit(ProductCategory $category)
    {
        $categories = ProductCategory::where('id', '!=', $category->id)->root()->get();
        return view('admins.product.categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, ProductCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:product_categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:product_categories,id',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = uploadFile($request->file('image'),'categories');
        }

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy(ProductCategory $category)
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with existing products!'
            ]);
        }

        $category->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted Successfully'
        ]);
    }

    public function getCategories(Request $request)
    {
        if ($request->ajax()) {
            $categories = ProductCategory::with(['parent', 'children'])->select('product_categories.*');

            return DataTables::of($categories)
                ->addIndexColumn()
                ->editColumn('name', function($row) {
                    $html = '<div class="d-flex align-items-center">';
                    if ($row->image) {
                        $html .= '<img src="'.$row->image.'" class="me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">';
                    } else {
                        $html .= '<div class="me-2 bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 4px;"><i class="ti-image text-muted"></i></div>';
                    }
                    $html .= '<div>';
                    $html .= '<strong>'.$row->name.'</strong>';
                    if ($row->parent) {
                        $html .= '<br><small class="text-muted">Parent: '.$row->parent->name.'</small>';
                    }
                    if ($row->slug) {
                        $html .= '<br><small class="text-info">/'.$row->slug.'</small>';
                    }
                    $html .= '</div></div>';
                    return $html;
                })
                ->editColumn('is_active', function($row) {
                    return $row->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('products_count', function($row) {
                    $count = $row->products()->count();
                    if ($count > 0) {
                        return '<span class="badge bg-primary">'.$count.' products</span>';
                    }
                    return '<span class="text-muted">0 products</span>';
                })
                ->editColumn('sort_order', function($row) {
                    return $row->sort_order ?? '<span class="text-muted">Not set</span>';
                })
                ->addColumn('actions', function($row) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="'.route('categories.show', $row->id).'">
                                    <i class="ti-eye"></i> View
                                </a></li>
                                <li><a class="dropdown-item" href="'.route('categories.edit', $row->id).'">
                                    <i class="ti-pencil"></i> Edit
                                </a></li>
                            </ul>
                        </div>';
                })
                ->rawColumns(['name', 'is_active', 'products_count', 'sort_order', 'actions'])
                ->make(true);
        }
    }
}
