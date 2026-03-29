<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CoachInfoController extends Controller
{
    public function profileInfo()
    {
        $coach = User::with('profile')->where('id', Auth::Id())
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['coach', 'nutritionist']);
            })
            ->first();

        if (!$coach) {
            return response()->json([
                'status' => false,
                'message' => 'Coach or Nutritionist not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Profile information retrieved successfully',
            'data' => $coach,
        ]);
    }
}
