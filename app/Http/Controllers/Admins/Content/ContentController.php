<?php

namespace App\Http\Controllers\Admins\Content;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\MealPlan;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ContentController extends Controller
{
    public function index()
    {
        $totalArticles = Article::count();
        $totalMealPlans = MealPlan::count();
        $totalRecipes = Recipe::count();

        return view('admins.content.index', compact('totalArticles', 'totalMealPlans', 'totalRecipes'));
    }

    // Article Management
    public function articleIndex()
    {
        return view('admins.content.articles.index');
    }

    public function articleCreate()
    {
        $authors = User::role(['admin', 'coach', 'nutritionist'])->get();
        return view('admins.content.articles.create', compact('authors'));
    }

    public function articleStore(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'author_id' => 'required|exists:users,id',
            'published_at' => 'nullable|date',
        ]);

        Article::create($validated);

        return redirect()->route('content.articles.index')
            ->with('success', 'Article created successfully!');
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
                            <strong>'.$row->title.'</strong><br>
                            <small class="text-muted">'.Str::limit(strip_tags($row->body), 50).'</small>
                        </div>';
                })
                ->editColumn('author_id', function($row) {
                    return $row->author ? $row->author->name : 'Unknown';
                })
                ->editColumn('published_at', function($row) {
                    if ($row->published_at) {
                        return '<span class="badge bg-success">Published</span><br><small>'.$row->published_at->format('M d, Y').'</small>';
                    }
                    return '<span class="badge bg-warning">Draft</span>';
                })
                ->addColumn('actions', function($row) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="'.route('content.articles.edit', $row->id).'">
                                    <i class="ti-pencil"></i> Edit
                                </a></li>
                                <li>
                                    <form action="'.route('content.articles.destroy', $row->id).'" method="POST" class="d-inline">
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
                ->rawColumns(['title', 'published_at', 'actions'])
                ->make(true);
        }
    }

    // Recipe Management
    public function recipeIndex()
    {
        return view('admins.content.recipes.index');
    }

    public function recipeCreate()
    {
        return view('admins.content.recipes.create');
    }

    public function recipeStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'instructions' => 'required|string',
            'ingredients' => 'required|json',
            'calories' => 'required|integer|min:0',
            'protein_grams' => 'nullable|numeric|min:0',
            'carbs_grams' => 'nullable|numeric|min:0',
            'fat_grams' => 'nullable|numeric|min:0',
            'prep_time_minutes' => 'required|integer|min:0',
            'cook_time_minutes' => 'required|integer|min:0',
        ]);

        Recipe::create($validated);

        return redirect()->route('content.recipes.index')
            ->with('success', 'Recipe created successfully!');
    }

    public function getRecipes(Request $request)
    {
        if ($request->ajax()) {
            $recipes = Recipe::select('*');

            return DataTables::of($recipes)
                ->addIndexColumn()
                ->editColumn('name', function($row) {
                    return '
                        <div>
                            <strong>'.$row->name.'</strong><br>
                            <small class="text-muted">'.$row->calories.' calories</small>
                        </div>';
                })
                ->addColumn('nutrition', function($row) {
                    return '
                        <div>
                            <small>P: '.($row->protein_grams ?? 0).'g | C: '.($row->carbs_grams ?? 0).'g | F: '.($row->fat_grams ?? 0).'g</small>
                        </div>';
                })
                ->addColumn('time', function($row) {
                    return '
                        <div>
                            <small>Prep: '.$row->prep_time_minutes.'min<br>Cook: '.$row->cook_time_minutes.'min</small>
                        </div>';
                })
                ->addColumn('actions', function($row) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="'.route('content.recipes.edit', $row->id).'">
                                    <i class="ti-pencil"></i> Edit
                                </a></li>
                                <li>
                                    <form action="'.route('content.recipes.destroy', $row->id).'" method="POST" class="d-inline">
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
                ->rawColumns(['name', 'nutrition', 'time', 'actions'])
                ->make(true);
        }
    }
}
