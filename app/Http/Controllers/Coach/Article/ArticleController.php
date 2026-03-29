<?php

namespace App\Http\Controllers\Coach\Article;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use App\Models\Plan;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ArticleController extends Controller
{
    public function index()
    {
        $totalArticles = Article::count();
        $publishedArticles = Article::whereNotNull('published_at')->count();
        $draftArticles = Article::whereNull('published_at')->count();

        return view('coach.article.index', compact('totalArticles', 'publishedArticles', 'draftArticles'));
    }

    public function create()
    {
         $tags = Tag::get();
        $authors = User::role(['admin', 'coach', 'nutritionist'])->get();
        return view('coach.article.create', get_defined_vars());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'published_at' => 'required|date',
             'tags.*'=> 'exists:tags,id',

        ]);

        $validated['author_id'] = auth()->user()->id;

        // Generate unique slug
        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;

        while (Article::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $validated['slug'] = $slug;

        $article  = Article::create($validated);

        if ($request->has('tags')) {
            $article->tags()->sync($request->tags);
        }

        return redirect()->route('article.index')
            ->with('success', 'Article created successfully!');
    }

    public function show(Article $article)
    {
        $plans = Plan::get();
        $article->load('author');
        return view('coach.article.show', get_defined_vars());
    }

    public function edit(Article $article)
    {
        $tags = Tag::get();
        $selectedTags = $workout->tags->pluck('id')->toArray();
        $authors = User::role(['admin', 'coach', 'nutritionist'])->get();
        return view('coach.article.edit', get_defined_vars());
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'published_at' => 'required|date',
            'tags.*'=> 'exists:tags,id',

        ]);

        $validated['author_id'] = auth()->user()->id;

        // Update slug if title changed
        if ($validated['title'] !== $article->title) {
            $slug = Str::slug($validated['title']);
            $originalSlug = $slug;
            $counter = 1;

            while (Article::where('slug', $slug)->where('id', '!=', $article->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $validated['slug'] = $slug;
        }

        $article->update($validated);

        if ($request->has('tags')) {
            $article->tags()->sync($request->tags);
        }

        return redirect()->route('article.index')
            ->with('success', 'Article updated successfully!');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return response()->json([
            'success' => true,
            'message' => 'Article deleted successfully!'
        ]);
        return redirect()->route('article.index')
            ->with('success', 'Article deleted successfully!');
    }

    public function getArticles(Request $request)
    {
        if ($request->ajax()) {
            $articles = Article::with('author');

            return DataTables::of($articles)
                ->addIndexColumn()
                ->editColumn('title', function($row) {
                    return '
                        <div>
                            <strong>' . Str::limit($row->title, 50) . '</strong><br>
                            <small class="text-muted">Slug: ' . $row->slug . '</small>
                        </div>';
                })
                ->editColumn('author_id', function($row) {
                    return '
                        <div>
                            <strong>' . @$row->author->name . '</strong><br>
                            <small class="text-muted">' . @$row->author->email . '</small>
                        </div>';
                })
                ->editColumn('published_at', function($row) {
                    if ($row->published_at) {
                        return '
                            <div>
                                <span class="badge bg-success">Published</span><br>
                                <small class="text-muted">' . $row->published_at->format('M d, Y g:i A') . '</small>
                            </div>';
                    } else {
                        return '<span class="badge bg-warning">Draft</span>';
                    }
                })
                ->editColumn('body', function($row) {
                    return Str::limit(strip_tags($row->body), 100);
                })
                ->addColumn('actions', function($row) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="' . route('article.show', $row->id) . '">
                                    <i class="ti-eye"></i> View
                                </a></li>
                                <li><a class="dropdown-item" href="' . route('article.edit', $row->id) . '">
                                    <i class="ti-pencil"></i> Edit
                                </a></li>
                            </ul>
                        </div>';
                })
                ->rawColumns(['title', 'author_id', 'published_at', 'actions'])
                ->make(true);
        }
    }

    public function publish(Article $article)
    {
        $article->update(['published_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Article published successfully!'
        ]);
    }

    public function unpublish(Article $article)
    {
        $article->update(['published_at' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Article unpublished successfully!'
        ]);
    }

    public function togglePublish(Request $request, Article $article)
    {
        $published = $article->published_at ? null : now();
        $article->update(['published_at' => $published]);

        return response()->json([
            'success' => true,
            'message' => $published ? 'Article published successfully!' : 'Article unpublished successfully!',
            'status' => $published ? 'published' : 'draft'
        ]);
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:publish,unpublish,delete',
            'articles' => 'required|array',
            'articles.*' => 'exists:articles,id'
        ]);

        $articles = Article::whereIn('id', $validated['articles']);

        switch ($validated['action']) {
            case 'publish':
                $articles->update(['published_at' => now()]);
                $message = 'Articles published successfully!';
                break;
            case 'unpublish':
                $articles->update(['published_at' => null]);
                $message = 'Articles unpublished successfully!';
                break;
            case 'delete':
                $articles->delete();
                $message = 'Articles deleted successfully!';
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}
