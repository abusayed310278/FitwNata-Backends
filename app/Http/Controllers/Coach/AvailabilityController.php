<?php

namespace App\Http\Controllers\Coach;

use Carbon\Carbon;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\CoachAvailabilities;
use App\Http\Controllers\Controller;

class AvailabilityController extends Controller
{
    /**
     * Display availability settings
     */
    public function index()
    {
        $coach = auth()->user();

        // Get current availability settings
        $availabilities = $coach->availabilities()
            ->where('is_blocked', false)
            ->orderByRaw("FIELD(day_of_week, 'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday')")
            ->orderBy('start_time')
            ->get()
            ->groupBy(function($item) {
                // Convert day name into its index (0 = Sunday … 6 = Saturday)
                return array_search($item->day_of_week, [
                    'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'
                ]);
            });

        // Days of the week
        $daysOfWeek = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday'
        ];

        // Blocked times/unavailable periods
        $blockedTimes = CoachAvailabilities::where('coach_id', $coach->id)
            ->where('is_blocked', true)
            ->orderBy('blocked_date')
            ->get();

        return view('coach.availability.index', compact(
            'availabilities',
            'daysOfWeek',
            'blockedTimes'
        ));
    }

    /**
     * Update availability settings
     */
    public function update(Request $request): JsonResponse
    {
        $validDays = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

        $validated = $request->validate([
            'availability'                 => 'required|array|min:1',
            'availability.*.day'           => ['required','string', Rule::in($validDays)],
            'availability.*.enabled'       => 'sometimes|in:true,false,1,0',
            'availability.*.start_time'    => 'required',
            'availability.*.end_time'      => 'required|after:start_time',
        ], [
            'availability.*.day.in'        => 'Day must be one of: '.implode(', ', $validDays).'.',
            'availability.*.end_time.after'=> 'End time must be after start time for each slot.',
        ]);

        $coach = auth()->user();

        DB::transaction(function () use ($coach, $validated) {
            $coach->availabilities()->where('is_blocked', false)->delete();

            foreach ($validated['availability'] as $slot) {
                $enabled = filter_var($slot['enabled'] ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                $enabled = ($enabled === null) ? true : $enabled;
                if (!$enabled) continue;

                \App\Models\CoachAvailabilities::create([
                    'coach_id'    => $coach->id,
                    'day_of_week' => $slot['day'],              // <-- day name
                    'start_time'  => $slot['start_time'],       // TIME columns accept HH:MM or HH:MM:SS
                    'end_time'    => $slot['end_time'],
                    'is_blocked'  => false,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Availability settings updated successfully',
        ]);
    }

    /**
     * Block specific time period
     */
    public function blockTime(Request $request): JsonResponse
    {
        $request->validate([
            'blocked_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'nullable|string|max:255',
        ]);

        $coach = auth()->user();

        // Check if there are any approved appointments in this time slot
        $conflictingAppointments = \App\Models\Appointment::where('coach_id', $coach->id)
            ->whereDate('scheduled_at', $request->blocked_date)
            ->where('status', 'approved')
            ->whereBetween('scheduled_at', [
                Carbon::parse($request->blocked_date . ' ' . $request->start_time),
                Carbon::parse($request->blocked_date . ' ' . $request->end_time)
            ])
            ->exists();


        if ($conflictingAppointments) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot block time - there are existing approved appointments in this slot'
            ]);
        }


        CoachAvailabilities::create([
            'coach_id' => $coach->id,
            'blocked_date' => $request->blocked_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_blocked' => true,
            'notes' => $request->reason,
        ]);


        return response()->json([
            'success' => true,
            'message' => 'Time slot blocked successfully'
        ]);
    }

    /**
     * Unblock time period
     */
    public function unblock($id): JsonResponse
    {
        $blockedTime = CoachAvailabilities::where('id', $id)
            ->where('coach_id', auth()->id())
            ->where('is_blocked', true)
            ->first();

        if (!$blockedTime) {
            return response()->json([
                'success' => false,
                'message' => 'Blocked time not found'
            ]);
        }

        $blockedTime->delete();

        return response()->json([
            'success' => true,
            'message' => 'Time slot unblocked successfully'
        ]);
    }
}
