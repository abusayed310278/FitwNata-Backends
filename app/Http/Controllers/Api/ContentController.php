<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ContentController extends BaseApiController
{
    /**
     * Get featured content for app preview.
     */
    public function featured(): JsonResponse
    {
        $articles = Article::whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return $this->success([
            'featured_articles' => $articles,
            'app_info' => [
                'name' => 'FitwNata',
                'description' => 'Your personal fitness and wellness companion',
                'features' => [
                    'Personalized workout plans',
                    'Nutrition guidance',
                    'Coach consultations',
                    'Progress tracking',
                    'Wellness products'
                ]
            ]
        ], 'Featured content retrieved');
    }

    /**
     * Get all articles.
     */
    public function articles(Request $request): JsonResponse
    {
        $query = Article::whereNotNull('published_at');

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('body', 'like', '%' . $request->search . '%');
        }

        $articles = $query->orderBy('published_at', 'desc')
            ->paginate(10);

        return $this->paginatedSuccess($articles, 'Articles retrieved successfully');
    }

    /**
     * Get article details.
     */
    public function articleDetails(Article $article): JsonResponse
    {
        if (!$article->published_at) {
            return $this->notFound('Article not found');
        }

        $article->load('author');

        return $this->success($article, 'Article details retrieved');
    }

    /**
     * Get fitness videos.
     */
    public function videos(): JsonResponse
    {
        // For now, return sample video data
        // In production, you might have a Video model
        $videos = [
            [
                'id' => 1,
                'title' => '10-Minute Morning Workout',
                'description' => 'Start your day with this energizing routine',
                'duration' => '10:00',
                'thumbnail' => 'videos/thumbs/morning-workout.jpg',
                'url' => 'videos/morning-workout.mp4',
                'category' => 'workout'
            ],
            [
                'id' => 2,
                'title' => 'Healthy Breakfast Ideas',
                'description' => 'Quick and nutritious breakfast recipes',
                'duration' => '8:30',
                'thumbnail' => 'videos/thumbs/breakfast-ideas.jpg',
                'url' => 'videos/breakfast-ideas.mp4',
                'category' => 'nutrition'
            ],
            [
                'id' => 3,
                'title' => 'Stress Relief Meditation',
                'description' => 'Calm your mind with guided meditation',
                'duration' => '15:00',
                'thumbnail' => 'videos/thumbs/meditation.jpg',
                'url' => 'videos/meditation.mp4',
                'category' => 'wellness'
            ]
        ];

        return $this->success($videos, 'Videos retrieved successfully');
    }

    /**
     * Get fitness tips.
     */
    public function tips(): JsonResponse
    {
        $tips = [
            [
                'id' => 1,
                'title' => 'Stay Hydrated',
                'content' => 'Drink at least 8 glasses of water daily to maintain optimal hydration.',
                'category' => 'general',
                'icon' => 'water-drop'
            ],
            [
                'id' => 2,
                'title' => 'Get Enough Sleep',
                'content' => 'Aim for 7-9 hours of quality sleep each night for better recovery.',
                'category' => 'recovery',
                'icon' => 'moon'
            ],
            [
                'id' => 3,
                'title' => 'Warm Up Before Exercise',
                'content' => 'Always start with 5-10 minutes of light cardio to prepare your body.',
                'category' => 'workout',
                'icon' => 'fire'
            ],
            [
                'id' => 4,
                'title' => 'Eat Protein After Workout',
                'content' => 'Consume protein within 30 minutes post-workout for muscle recovery.',
                'category' => 'nutrition',
                'icon' => 'nutrition'
            ]
        ];

        return $this->success($tips, 'Tips retrieved successfully');
    }

    /**
     * Like an article.
     */
    public function likeArticle(Request $request, Article $article): JsonResponse
    {
        $user = $request->user();

        // You might want to create a UserLike model for this
        // For now, we'll just return success

        return $this->success([
            'article_id' => $article->id,
            'is_liked' => true,
            'likes_count' => 1 // You would calculate this from the database
        ], 'Article liked successfully');
    }

    /**
     * Bookmark an article.
     */
    public function bookmarkArticle(Request $request, Article $article): JsonResponse
    {
        $user = $request->user();

        // You might want to create a UserBookmark model for this
        // For now, we'll just return success

        return $this->success([
            'article_id' => $article->id,
            'is_bookmarked' => true
        ], 'Article bookmarked successfully');
    }

    /**
     * Get user bookmarks.
     */
    public function bookmarks(Request $request): JsonResponse
    {
        // For now, return empty array
        // In production, you would fetch user's bookmarked articles

        return $this->success([], 'Bookmarks retrieved successfully');
    }
}
