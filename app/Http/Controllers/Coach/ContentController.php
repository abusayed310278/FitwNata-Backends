<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    /**
     * Display content manager
     */
    public function index()
    {
        $coach = auth()->user();

        // Content statistics
        $stats = [
            'total' => $coach->articles()->count(),
            'published' => $coach->articles()->whereNotNull('published_at')->count(),
            'drafts' => $coach->articles()->whereNull('published_at')->count(),
            'this_month' => $coach->articles()
                ->whereMonth('created_at', now()->month)
                ->count(),
        ];

        return view('coach.content.index', compact('stats'));
    }

    /**
     * Show create content form
     */
    public function create()
    {
        // Get available user tiers/groups for targeting
        $userTiers = [
            'all' => 'All Users',
            'premium' => 'Premium Subscribers',
            'basic' => 'Basic Subscribers',
            'trial' => 'Trial Users'
        ];

        $contentTypes = [
            'workout_video' => 'Workout Video',
            'nutrition_tip' => 'Nutrition Tip',
            'blog_post' => 'Blog Post',
            'recipe' => 'Recipe',
            'motivation' => 'Motivational Content'
        ];

        return view('coach.content.create', compact('userTiers', 'contentTypes'));
    }

    /**
     * Store new content
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content_type' => 'required|string|in:workout_video,nutrition_tip,blog_post,recipe,motivation',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'tags' => 'nullable|string',
            'target_audience' => 'required|string|in:all,premium,basic,trial',
            'scheduled_publish' => 'nullable|date|after:now',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'video_url' => 'nullable|url',
            'status' => 'required|in:draft,published,scheduled'
        ]);

        $coach = auth()->user();

        $articleData = [
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'content_type' => $request->content_type,
            'tags' => $request->tags,
            'target_audience' => $request->target_audience,
            'author_id' => $coach->id,
            'status' => $request->status,
        ];

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('content/images', 'public');
            $articleData['featured_image'] = $path;
        }

        // Handle video URL
        if ($request->video_url) {
            $articleData['video_url'] = $request->video_url;
        }

        // Set publish date
        if ($request->status === 'published') {
            $articleData['published_at'] = now();
        } elseif ($request->status === 'scheduled' && $request->scheduled_publish) {
            $articleData['published_at'] = $request->scheduled_publish;
        }

        Article::create($articleData);

        return redirect()->route('coach.content.index')
            ->with('success', 'Content created successfully!');
    }

    /**
     * Show edit content form
     */
    public function edit(Article $article)
    {
        // Ensure coach can only edit their own content
        if ($article->author_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $userTiers = [
            'all' => 'All Users',
            'premium' => 'Premium Subscribers',
            'basic' => 'Basic Subscribers',
            'trial' => 'Trial Users'
        ];

        $contentTypes = [
            'workout_video' => 'Workout Video',
            'nutrition_tip' => 'Nutrition Tip',
            'blog_post' => 'Blog Post',
            'recipe' => 'Recipe',
            'motivation' => 'Motivational Content'
        ];

        return view('coach.content.edit', compact('article', 'userTiers', 'contentTypes'));
    }

    /**
     * Update content
     */
    public function update(Request $request, Article $article)
    {
        // Ensure coach can only edit their own content
        if ($article->author_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content_type' => 'required|string|in:workout_video,nutrition_tip,blog_post,recipe,motivation',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'tags' => 'nullable|string',
            'target_audience' => 'required|string|in:all,premium,basic,trial',
            'scheduled_publish' => 'nullable|date|after:now',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'video_url' => 'nullable|url',
            'status' => 'required|in:draft,published,scheduled'
        ]);

        $updateData = [
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'content_type' => $request->content_type,
            'tags' => $request->tags,
            'target_audience' => $request->target_audience,
            'status' => $request->status,
        ];

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('content/images', 'public');
            $updateData['featured_image'] = $path;
        }

        // Handle video URL
        if ($request->video_url) {
            $updateData['video_url'] = $request->video_url;
        }

        // Set publish date
        if ($request->status === 'published' && !$article->published_at) {
            $updateData['published_at'] = now();
        } elseif ($request->status === 'scheduled' && $request->scheduled_publish) {
            $updateData['published_at'] = $request->scheduled_publish;
        } elseif ($request->status === 'draft') {
            $updateData['published_at'] = null;
        }

        $article->update($updateData);

        return redirect()->route('coach.content.index')
            ->with('success', 'Content updated successfully!');
    }

    /**
     * Delete content
     */
    public function destroy(Article $article): JsonResponse
    {
        // Ensure coach can only delete their own content
        if ($article->author_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $article->delete();

        return response()->json([
            'success' => true,
            'message' => 'Content deleted successfully'
        ]);
    }

    /**
     * Upload media files
     */
    public function uploadMedia(Request $request): JsonResponse
    {
        $request->validate([
            'media' => 'required|file|mimes:jpeg,png,jpg,webp,mp4,mov,avi|max:10240', // 10MB max
        ]);

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $path = $file->store('content/media', 'public');

            return response()->json([
                'success' => true,
                'url' => asset('storage/' . $path),
                'path' => $path
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded']);
    }

    /**
     * Get content data for DataTables
     */
    public function getContent(Request $request)
    {
        if ($request->ajax()) {
            $content = Article::where('author_id', auth()->id())
                ->select('articles.*');

            return DataTables::of($content)
                ->addIndexColumn()
                ->editColumn('title', function($row) {
                    $statusBadge = match($row->status) {
                        'published' => '<span class="badge bg-success ms-2">Published</span>',
                        'draft' => '<span class="badge bg-warning ms-2">Draft</span>',
                        'scheduled' => '<span class="badge bg-info ms-2">Scheduled</span>',
                        default => ''
                    };
                    return '<strong>'.$row->title.'</strong>' . $statusBadge;
                })
                ->editColumn('content_type', function($row) {
                    $types = [
                        'workout_video' => 'Workout Video',
                        'nutrition_tip' => 'Nutrition Tip',
                        'blog_post' => 'Blog Post',
                        'recipe' => 'Recipe',
                        'motivation' => 'Motivational'
                    ];
                    return '<span class="badge bg-primary">'.($types[$row->content_type] ?? $row->content_type).'</span>';
                })
                ->editColumn('target_audience', function($row) {
                    return '<span class="badge bg-secondary">'.ucfirst($row->target_audience).'</span>';
                })
                ->editColumn('published_at', function($row) {
                    if ($row->published_at) {
                        return $row->published_at->format('M d, Y g:i A');
                    }
                    return '<span class="text-muted">Not published</span>';
                })
                ->addColumn('actions', function($row) {
                    return '
                        <div class="btn-group btn-group-sm">
                            <a href="'.route('coach.content.edit', $row->id).'" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-danger" onclick="deleteContent('.$row->id.')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>';
                })
                ->rawColumns(['title', 'content_type', 'target_audience', 'published_at', 'actions'])
                ->make(true);
        }
    }
}
