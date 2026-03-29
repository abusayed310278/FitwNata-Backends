<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;
use Yajra\DataTables\Facades\DataTables;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::latest()->paginate(10);
        return view('admins.tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
        ]);

        Tag::create($request->only('name'));

        return redirect()->route('tags.index')->with('success', 'Tag created successfully.');
    }

    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->id,
        ]);

        $tag->update($request->only('name'));

        return redirect()->route('tags.index')->with('success', 'Tag updated successfully.');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

         return response()->json([
            'success' => true,
            'message' => 'Tag deleted successfully!'
        ]);

        return redirect()->route('tags.index')->with('success', 'Tag deleted successfully.');
    }

     public function list(Request $request)
    {
        if ($request->ajax()) {
            $articles = Tag::query();

            return DataTables::of($articles)
                ->addIndexColumn()
                ->editColumn('title', function($row) {
                    return '
                        <div>
                            <strong>'.$row->name.'</strong><br>
                        </div>';
                })
                ->addColumn('actions', function($row) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <button class="dropdown-item text-warning edit-tag-btn"
                                        data-id="'.$row->id.'" data-name="'.e($row->name).'">
                                        <i class="ti-pencil"></i> Edit
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item text-danger delete-tag-btn"
                                        data-id="'.$row->id.'">
                                        <i class="ti-trash"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>';
                })
                ->rawColumns(['title', 'published_at', 'actions'])
                ->make(true);
        }
    }
}
