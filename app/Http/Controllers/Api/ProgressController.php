<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\UserMeasurement;
use App\Models\ProgressJournal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ProgressController extends BaseApiController
{
    /**
     * Get progress dashboard.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get latest measurements
        $latestMeasurement = $user->measurements()->latest()->first();

        // Get recent journal entries
        $recentEntries = $user->progressJournals()
            ->orderBy('entry_date', 'desc')
            ->take(5)
            ->get();

        // Calculate progress stats
        $totalEntries = $user->progressJournals()->count();
        $thisWeekEntries = $user->progressJournals()
            ->where('entry_date', '>=', Carbon::now()->startOfWeek())
            ->count();

        return $this->success([
            'latest_measurement' => $latestMeasurement,
            'recent_journal_entries' => $recentEntries,
            'stats' => [
                'total_journal_entries' => $totalEntries,
                'this_week_entries' => $thisWeekEntries,
            ]
        ], 'Progress dashboard retrieved');
    }

    /**
     * Get user measurements.
     */
    public function measurements(Request $request): JsonResponse
    {
        $measurements = $request->user()->measurements()
            ->orderBy('date', 'desc')
            ->paginate(10);

        return $this->paginatedSuccess($measurements, 'Measurements retrieved successfully');
    }

    /**
     * Add new measurement.
     */
    public function addMeasurement(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|before_or_equal:today',
            'weight' => 'nullable|numeric|min:30|max:300',
            'height' => 'nullable|numeric|min:100|max:250',
            'body_fat_percentage' => 'nullable|numeric|min:3|max:50',
            'muscle_mass' => 'nullable|numeric|min:10|max:100',
            'waist_circumference' => 'nullable|numeric|min:50|max:150',
            'chest_circumference' => 'nullable|numeric|min:60|max:180',
            'arm_circumference' => 'nullable|numeric|min:15|max:60',
            'thigh_circumference' => 'nullable|numeric|min:30|max:80',
            'notes' => 'nullable|string|max:1000',
            // Legacy fields for backward compatibility
            'weight_kg' => 'nullable|numeric|min:30|max:300',
            'bmi' => 'nullable|numeric|min:10|max:50',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        // Check if measurement for this date already exists
        $existingMeasurement = $request->user()->measurements()
            ->where('date', $request->date)
            ->first();

        $measurementData = [
            'user_id' => $request->user()->id,
            'date' => $request->date,
            'weight' => $request->weight,
            'height' => $request->height,
            'body_fat_percentage' => $request->body_fat_percentage,
            'muscle_mass' => $request->muscle_mass,
            'waist_circumference' => $request->waist_circumference,
            'chest_circumference' => $request->chest_circumference,
            'arm_circumference' => $request->arm_circumference,
            'thigh_circumference' => $request->thigh_circumference,
            'notes' => $request->notes,
            // Legacy fields
            'weight_kg' => $request->weight_kg ?? $request->weight,
            'bmi' => $request->bmi,
        ];

        if ($existingMeasurement) {
            $existingMeasurement->update($measurementData);
            $measurement = $existingMeasurement;
        } else {
            $measurement = UserMeasurement::create($measurementData);
        }

        return $this->success($measurement, 'Measurement saved successfully');
    }

    /**
     * Get journal entries.
     */
    public function journal(Request $request): JsonResponse
    {
        $entries = $request->user()->progressJournals()
            ->orderBy('entry_date', 'desc')
            ->paginate(10);

        return $this->paginatedSuccess($entries, 'Journal entries retrieved successfully');
    }

    /**
     * Add journal entry.
     */
    public function addJournalEntry(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'entry_date' => 'required|date|before_or_equal:today',
            'entry_type' => 'required|in:workout,nutrition,wellness,measurements,goals,coach_note',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string|max:2000',
            'mood_rating' => 'nullable|integer|min:1|max:5',
            'energy_level' => 'nullable|integer|min:1|max:5',
            'coach_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:1000', // Legacy field
            'mood' => 'nullable|integer|min:1|max:5', // Legacy field
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        // If coach_id is provided, verify the user is a coach
        if ($request->coach_id) {
            $coach = User::role('coach', 'web')->find($request->coach_id);
            if (!$coach) {
                return $this->error('Invalid coach selected');
            }
        }

        $entry = ProgressJournal::create([
            'user_id' => $request->user()->id,
            'entry_date' => $request->entry_date,
            'entry_type' => $request->entry_type,
            'title' => $request->title,
            'content' => $request->content,
            'mood_rating' => $request->mood_rating,
            'energy_level' => $request->energy_level,
            'coach_id' => $request->coach_id,
            'notes' => $request->notes, // Legacy field
            'mood' => $request->mood, // Legacy field
        ]);

        return $this->success($entry->load('coach'), 'Journal entry created successfully');
    }

    /**
     * Update journal entry.
     */
    public function updateJournalEntry(Request $request, ProgressJournal $entry): JsonResponse
    {
        if ($entry->user_id !== $request->user()->id) {
            return $this->forbidden('You can only update your own journal entries');
        }

        $validator = Validator::make($request->all(), [
            'entry_type' => 'sometimes|in:workout,nutrition,wellness,measurements,goals,coach_note',
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string|max:2000',
            'mood_rating' => 'sometimes|integer|min:1|max:5',
            'energy_level' => 'sometimes|integer|min:1|max:5',
            'notes' => 'sometimes|string|max:1000', // Legacy field
            'mood' => 'sometimes|integer|min:1|max:5', // Legacy field
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $entry->update($request->only([
            'entry_type', 'title', 'content', 'mood_rating', 'energy_level', 'notes', 'mood'
        ]));

        return $this->success($entry->load('coach'), 'Journal entry updated successfully');
    }

    /**
     * Delete journal entry.
     */
    public function deleteJournalEntry(Request $request, ProgressJournal $entry): JsonResponse
    {
        if ($entry->user_id !== $request->user()->id) {
            return $this->forbidden('You can only delete your own journal entries');
        }

        $entry->delete();

        return $this->success(null, 'Journal entry deleted successfully');
    }

    /**
     * Get progress statistics.
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        // Weight progress over time
        $measurements = $user->measurements()
            ->orderBy('date')
            ->get(['date', 'weight_kg']);

        // Journal entries per month
        $journalStats = $user->progressJournals()
            ->selectRaw('YEAR(entry_date) as year, MONTH(entry_date) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        return $this->success([
            'weight_progress' => $measurements,
            'journal_activity' => $journalStats,
        ], 'Progress statistics retrieved');
    }

    /**
     * Get achievements.
     */
    public function achievements(Request $request): JsonResponse
    {
        $user = $request->user();

        // Calculate achievements based on user activity
        $achievements = [];

        // Journal streak achievement
        $journalCount = $user->progressJournals()->count();
        if ($journalCount >= 7) {
            $achievements[] = [
                'id' => 'journal_week',
                'title' => 'Weekly Warrior',
                'description' => 'Logged 7 journal entries',
                'icon' => 'journal',
                'earned_at' => $user->progressJournals()->orderBy('created_at')->skip(6)->first()?->created_at,
            ];
        }

        if ($journalCount >= 30) {
            $achievements[] = [
                'id' => 'journal_month',
                'title' => 'Monthly Master',
                'description' => 'Logged 30 journal entries',
                'icon' => 'trophy',
                'earned_at' => $user->progressJournals()->orderBy('created_at')->skip(29)->first()?->created_at,
            ];
        }

        return $this->success($achievements, 'Achievements retrieved successfully');
    }

    /**
     * Get journal entries by type.
     */
    public function journalByType(Request $request, $type): JsonResponse
    {
        $validTypes = ['workout', 'nutrition', 'wellness', 'measurements', 'goals', 'coach_note'];

        if (!in_array($type, $validTypes)) {
            return $this->error('Invalid entry type');
        }

        $entries = $request->user()->progressJournals()
            ->where('entry_type', $type)
            ->with('coach')
            ->orderBy('entry_date', 'desc')
            ->paginate(10);

        return $this->paginatedSuccess($entries, ucfirst($type) . ' journal entries retrieved successfully');
    }

    /**
     * Get coach notes for user.
     */
    public function coachNotes(Request $request): JsonResponse
    {
        $notes = $request->user()->progressJournals()
            ->where('entry_type', 'coach_note')
            ->with('coach')
            ->orderBy('entry_date', 'desc')
            ->paginate(10);

        return $this->paginatedSuccess($notes, 'Coach notes retrieved successfully');
    }

    /**
     * Get detailed measurement progress.
     */
    public function measurementProgress(Request $request): JsonResponse
    {
        $measurements = $request->user()->measurements()
            ->orderBy('date')
            ->get();

        // Calculate progress trends
        $progress = [
            'weight_trend' => $this->calculateTrend($measurements, 'weight'),
            'body_fat_trend' => $this->calculateTrend($measurements, 'body_fat_percentage'),
            'muscle_mass_trend' => $this->calculateTrend($measurements, 'muscle_mass'),
            'measurements_by_date' => $measurements,
        ];

        return $this->success($progress, 'Measurement progress retrieved successfully');
    }

    /**
     * Update existing measurement.
     */
    public function updateMeasurement(Request $request, UserMeasurement $measurement): JsonResponse
    {
        if ($measurement->user_id !== $request->user()->id) {
            return $this->forbidden('You can only update your own measurements');
        }

        $validator = Validator::make($request->all(), [
            'weight' => 'sometimes|numeric|min:30|max:300',
            'height' => 'sometimes|numeric|min:100|max:250',
            'body_fat_percentage' => 'sometimes|numeric|min:3|max:50',
            'muscle_mass' => 'sometimes|numeric|min:10|max:100',
            'waist_circumference' => 'sometimes|numeric|min:50|max:150',
            'chest_circumference' => 'sometimes|numeric|min:60|max:180',
            'arm_circumference' => 'sometimes|numeric|min:15|max:60',
            'thigh_circumference' => 'sometimes|numeric|min:30|max:80',
            'notes' => 'sometimes|string|max:1000',
            'weight_kg' => 'sometimes|numeric|min:30|max:300',
            'bmi' => 'sometimes|numeric|min:10|max:50',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $measurement->update($request->only([
            'weight', 'height', 'body_fat_percentage', 'muscle_mass',
            'waist_circumference', 'chest_circumference', 'arm_circumference',
            'thigh_circumference', 'notes', 'weight_kg', 'bmi'
        ]));

        return $this->success($measurement, 'Measurement updated successfully');
    }

    /**
     * Delete measurement.
     */
    public function deleteMeasurement(Request $request, UserMeasurement $measurement): JsonResponse
    {
        if ($measurement->user_id !== $request->user()->id) {
            return $this->forbidden('You can only delete your own measurements');
        }

        $measurement->delete();

        return $this->success(null, 'Measurement deleted successfully');
    }

    /**
     * Calculate trend for a specific measurement field.
     */
    private function calculateTrend($measurements, $field)
    {
        if ($measurements->count() < 2) {
            return 'insufficient_data';
        }

        $recent = $measurements->take(-5); // Last 5 measurements
        $values = $recent->pluck($field)->filter()->values();

        if ($values->count() < 2) {
            return 'insufficient_data';
        }

        $first = $values->first();
        $last = $values->last();
        $change = $last - $first;
        $percentChange = ($first != 0) ? ($change / $first) * 100 : 0;

        if (abs($percentChange) < 2) {
            return 'stable';
        }

        return $percentChange > 0 ? 'increasing' : 'decreasing';
    }
}
