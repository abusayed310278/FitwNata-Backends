<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Workout;
use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExerciseController extends Controller
{
    /**
     * Get all workouts.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Exercise::query();

        $workouts = $query->paginate(10);

        return $this->paginatedSuccess($workouts, 'Exercises retrieved successfully');
    }

    /**
     * Get workout details.
     */
    public function show(Exercise $exercise): JsonResponse
    {
        return $this->success($exercise, 'Exercise details retrieved');
    }

}
