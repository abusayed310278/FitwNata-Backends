<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Appointment;
use App\Models\User;
use App\Models\CoachAvailabilities;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AppointmentController extends BaseApiController
{
    /**
     * Get user appointments.
     */
    public function index(Request $request): JsonResponse
    {
        $appointments = $request->user()->appointments()
            ->with(['coach', 'nutritionist'])
            ->orderBy('scheduled_at', 'desc')
            ->paginate(10);

        return $this->paginatedSuccess($appointments, 'Appointments retrieved successfully');
    }

    /**
     * Create new appointment.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'coach_id' => 'nullable|exists:users,id',
            'nutritionist_id' => 'nullable|exists:users,id',
            'appointment_type' => 'required|string|max:255',
            // 'scheduled_at' => 'required|date|after:now',
            'scheduled_at' => 'required',
            'duration_minutes' => 'required',
            // 'duration_minutes' => 'required|integer|min:15|max:180',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        // Ensure either coach_id or nutritionist_id is provided, but not both
        if (empty($request->coach_id) && empty($request->nutritionist_id)) {
            return $this->error('Please select either a coach or nutritionist');
        }

        if (!empty($request->coach_id) && !empty($request->nutritionist_id)) {
            return $this->error('Please select either a coach or nutritionist, not both');
        }

        $professionalId = $request->coach_id ?? $request->nutritionist_id;
        $professionalType = $request->coach_id ? 'coach' : 'nutritionist';

        // Check if professional is available
        $scheduledTime = Carbon::parse($request->scheduled_at);
        $dayOfWeek = $scheduledTime->format('l'); // Monday, Tuesday, etc.

        $availability = CoachAvailabilities::where('coach_id', $professionalId)
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', '<=', $scheduledTime->format('H:i:s'))
            ->where('end_time', '>=', $scheduledTime->format('H:i:s'))
            ->where('is_blocked', false)
            ->first();

        if (!$availability) {
            return $this->error(ucfirst($professionalType) . ' is not available at the selected time');
        }

        // Check for conflicts
        $conflict = Appointment::where(function($query) use ($request) {
                if ($request->coach_id) {
                    $query->where('coach_id', $request->coach_id);
                } else {
                    $query->where('nutritionist_id', $request->nutritionist_id);
                }
            })
            ->where('scheduled_at', $request->scheduled_at)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($conflict) {
            return $this->error(ucfirst($professionalType) . ' already has an appointment at this time');
        }

        $appointment = Appointment::create([
            'user_id' => $request->user()->id,
            'coach_id' => $request->coach_id,
            'nutritionist_id' => $request->nutritionist_id,
            'appointment_type' => $request->appointment_type,
            'scheduled_at' => $request->scheduled_at,
            'duration_minutes' => $request->duration_minutes,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return $this->success($appointment->load(['coach', 'nutritionist']), 'Appointment created successfully');
    }

    /**
     * Get available coaches.
     */
    public function availableCoaches(): JsonResponse
    {
        // Specify the web guard since roles were created with web guard
        $coaches = User::role('coach', 'web')
            ->with('availabilities')
            ->get();

        return $this->success($coaches, 'Available coaches retrieved');
    }


    /**
     * Get available nutritionists.
     */
    public function availableNutritionists(): JsonResponse
    {
        // Specify the web guard since roles were created with web guard
        $nutritionists = User::role('nutritionist', 'web')
            ->with('availabilities')
            ->get();

        return $this->success($nutritionists, 'Available nutritionists retrieved');
    }

    /**
     * Get all available professionals (coaches and nutritionists).
     */
    public function availableProfessionals(): JsonResponse
    {
        // Specify the web guard since roles were created with web guard
        $professionals = User::role(['coach', 'nutritionist'], 'web')
            ->with(['availabilities', 'roles'])
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'type' => $user->roles->first()->name ?? 'professional',
                    'availabilities' => $user->availabilities
                ];
            });

        return $this->success($professionals, 'Available professionals retrieved');
    }

    /**
     * Get professional availability (coaches or nutritionists).
     */
    public function professionalAvailability(User $professional): JsonResponse
    {
        $availabilities = $professional->availabilities;

        $schedule = [];
        foreach ($availabilities as $availability) {
            $schedule[] = [
                'day_of_week' => $availability->day_of_week,
                'start_time' => $availability->start_time,
                'end_time' => $availability->end_time,
                'is_blocked' => $availability->is_blocked,
                'blocked_date' => $availability->blocked_date,
                'notes' => $availability->notes,
            ];
        }

        return $this->success($schedule, 'Professional availability retrieved');
    }

    /**
     * Get appointment details.
     */
    public function show(Appointment $appointment): JsonResponse
    {
        if ($appointment->user_id !== auth()->id()) {
            return $this->forbidden('You can only view your own appointments');
        }

        return $this->success($appointment->load(['coach', 'nutritionist']), 'Appointment details retrieved');
    }

    /**
     * Update appointment.
     */
    public function update(Request $request, Appointment $appointment): JsonResponse
    {
        if ($appointment->user_id !== auth()->id()) {
            return $this->forbidden('You can only update your own appointments');
        }

        $validator = Validator::make($request->all(), [
            'scheduled_at' => 'sometimes|date|after:now',
            'notes' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $appointment->update($request->only(['scheduled_at', 'notes']));

        return $this->success($appointment, 'Appointment updated successfully');
    }

    /**
     * Cancel appointment.
     */
    public function cancel(Appointment $appointment): JsonResponse
    {
        // if ($appointment->user_id !== auth()->id()) {
        //     return $this->forbidden('You can only cancel your own appointments');
        // }

        $appointment->update(['status' => 'cancelled']);

        return $this->success(null, 'Appointment cancelled successfully');
    }

    /**
     * Get upcoming appointments.
     */
    public function upcoming(Request $request): JsonResponse
    {
        $appointments = $request->user()->appointments()
            ->with(['coach', 'nutritionist'])
            ->where('scheduled_at', '>', now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('scheduled_at')
            ->get();

        return $this->success($appointments, 'Upcoming appointments retrieved');
    }

    /**
     * Get appointment history.
     */
    public function history(Request $request): JsonResponse
    {
        $appointments = $request->user()->appointments()
            ->with(['coach', 'nutritionist'])
            ->where(function ($query) {
                $query->where('scheduled_at', '<', now())
                    ->orWhereIn('status', ['completed', 'cancelled']);
            })
            ->orderBy('scheduled_at', 'desc')
            ->paginate(10);

        return $this->paginatedSuccess($appointments, 'Appointment history retrieved');
    }
}
