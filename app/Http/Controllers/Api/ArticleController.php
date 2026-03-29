<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ArticleController extends BaseApiController
{
    /**
     * Get published articles.
     */
    public function index(Request $request): JsonResponse
    {
        $articles = Article::published()
            ->with('author:id,name')
            ->select('id', 'title', 'slug', 'body', 'author_id', 'published_at', 'created_at')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return $this->paginatedSuccess($articles, 'Articles retrieved successfully');
    }

    /**
     * Get article by slug or ID.
     */
    public function show(Request $request, $identifier): JsonResponse
    {
        // Try to find by slug first, then by ID
        $article = Article::published()
            ->with('author:id,name,email')
            ->where(function($query) use ($identifier) {
                $query->where('slug', $identifier)
                      ->orWhere('id', $identifier);
            })
            ->first();

        if (!$article) {
            return $this->notFound('Article not found');
        }

        // Track article view (optional analytics)
        $this->trackArticleView($article, $request);

        return $this->success($article, 'Article retrieved successfully');
    }

    /**
     * Get featured articles.
     */
    public function featured(): JsonResponse
    {
        $cacheKey = 'featured_articles';

        $articles = Cache::remember($cacheKey, 3600, function () {
            return Article::published()
                ->with('author:id,name')
                ->select('id', 'title', 'slug', 'body', 'author_id', 'published_at')
                ->orderBy('published_at', 'desc')
                ->take(5)
                ->get()
                ->map(function($article) {
                    $article->excerpt = strip_tags(substr($article->body, 0, 200)) . '...';
                    return $article;
                });
        });

        return $this->success($articles, 'Featured articles retrieved successfully');
    }

    /**
     * Search articles.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (empty($query)) {
            return $this->error('Search query is required');
        }

        $articles = Article::published()
            ->with('author:id,name')
            ->where(function($queryBuilder) use ($query) {
                $queryBuilder->where('title', 'LIKE', "%{$query}%")
                           ->orWhere('body', 'LIKE', "%{$query}%");
            })
            ->select('id', 'title', 'slug', 'body', 'author_id', 'published_at')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return $this->paginatedSuccess($articles, 'Search results retrieved successfully');
    }

    /**
     * Get related articles.
     */
    public function related(Request $request, $id): JsonResponse
    {
        $article = Article::find($id);

        if (!$article) {
            return $this->notFound('Article not found');
        }

        // Simple related articles based on similar keywords in title
        $keywords = str_word_count($article->title, 1);
        $relatedQuery = Article::published()
            ->with('author:id,name')
            ->where('id', '!=', $id)
            ->select('id', 'title', 'slug', 'body', 'author_id', 'published_at');

        foreach ($keywords as $keyword) {
            if (strlen($keyword) > 3) { // Only consider words longer than 3 characters
                $relatedQuery->orWhere('title', 'LIKE', "%{$keyword}%");
            }
        }

        $relatedArticles = $relatedQuery
            ->orderBy('published_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($article) {
                $article->excerpt = strip_tags(substr($article->body, 0, 150)) . '...';
                return $article;
            });

        return $this->success($relatedArticles, 'Related articles retrieved successfully');
    }

    /**
     * Get latest articles by category/type.
     */
    public function byCategory(Request $request, $category): JsonResponse
    {
        $validCategories = ['fitness', 'nutrition', 'wellness', 'recipes', 'workouts'];

        if (!in_array($category, $validCategories)) {
            return $this->error('Invalid category');
        }

        // For now, we'll use title/content matching since we don't have a category field
        // In a real implementation, you'd add a category field to the articles table
        $categoryKeywords = [
            'fitness' => ['fitness', 'exercise', 'workout', 'training'],
            'nutrition' => ['nutrition', 'diet', 'food', 'meal', 'eating'],
            'wellness' => ['wellness', 'health', 'mental', 'stress', 'sleep'],
            'recipes' => ['recipe', 'cooking', 'meal', 'ingredient'],
            'workouts' => ['workout', 'exercise', 'training', 'gym', 'strength']
        ];

        $keywords = $categoryKeywords[$category] ?? [];

        $articles = Article::published()
            ->with('author:id,name')
            ->where(function($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('title', 'LIKE', "%{$keyword}%")
                          ->orWhere('body', 'LIKE', "%{$keyword}%");
                }
            })
            ->select('id', 'title', 'slug', 'body', 'author_id', 'published_at')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return $this->paginatedSuccess($articles, ucfirst($category) . ' articles retrieved successfully');
    }

    /**
     * Get article statistics.
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_articles' => Article::published()->count(),
            'this_month_articles' => Article::published()
                ->whereMonth('published_at', now()->month)
                ->whereYear('published_at', now()->year)
                ->count(),
            'authors_count' => Article::published()
                ->distinct('author_id')
                ->count('author_id'),
            'latest_article' => Article::published()
                ->with('author:id,name')
                ->latest('published_at')
                ->first(['id', 'title', 'slug', 'author_id', 'published_at'])
        ];

        return $this->success($stats, 'Article statistics retrieved successfully');
    }

    /**
     * Track article view for analytics.
     */
    private function trackArticleView(Article $article, Request $request)
    {
        // Simple view tracking - in production, you might want to use a more sophisticated system
        $cacheKey = "article_view_{$article->id}_{$request->ip()}";

        if (!Cache::has($cacheKey)) {
            // Log the view - you could store this in a database table for analytics
            Cache::put($cacheKey, true, 3600); // Prevent duplicate views from same IP for 1 hour

            // Here you could increment a view count in the database
            // $article->increment('view_count');
        }
    }
}
