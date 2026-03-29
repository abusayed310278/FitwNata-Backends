<?php

namespace App\Http\Controllers\Admins\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\CoachRequest;
use App\Models\User;
use App\Models\CoachAvailabilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\File;

class CoachController extends Controller
{
    /**
     * Display a listing of coaches.
     */
    public function index()
    {
        return view('admins.coach.index');
    }

    /**
     * Show the form for creating a new coach.
     */
    public function create()
    {
        // Days of the week for availability settings
        $daysOfWeek = [
            'Sunday', 'Monday', 'Tuesday', 'Wednesday',
            'Thursday', 'Friday', 'Saturday'
        ];

        // dd($daysOfWeek);

        return view('admins.coach.create', compact('daysOfWeek'));
    }

    /**
     * Store a newly created coach in storage.
     */
    public function store(CoachRequest $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'display_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
            'whatsapp' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',

            'availability' => 'nullable|array',
            'availability.*.*.enabled' => 'nullable|boolean',
            'availability.*.*.start_time' => 'nullable|date_format:H:i',
            'availability.*.*.end_time' => 'nullable|date_format:H:i',

            'blocked_times' => 'nullable|array',
            'blocked_times.*.date' => 'nullable|date',
            'blocked_times.*.start_time' => 'nullable|date_format:H:i',
            'blocked_times.*.end_time' => 'nullable|date_format:H:i',
            'blocked_times.*.reason' => 'nullable|string|max:255',
        ]);

        // dd($request->all());

        $coach = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'display_name' => $request->display_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'whatsapp' => $request->whatsapp,
            'bio' => $request->bio,
        ]);

        $coach->syncRoles(['coach']);

        if ($request->has('availability')) {
            $this->createAvailabilitySettings($coach, $request->availability);
        }

        if ($request->has('blocked_times')) {
            $this->createBlockedTimes($coach, $request->blocked_times);
        }

        return redirect()->route('coach.index')
            ->with('success', 'Coach created successfully!');
    }

    /**
     * Display the specified coach.
     */
    public function show(User $coach)
    {
        // Ensure the user is a coach
        if (!$coach->hasRole('coach')) {
            return redirect()->route('coach.index')
                ->with('error', 'User is not a coach.');
        }

        $coach->load(['availabilities', 'appointmentsAsCoach', 'articles']);

        // dd($coach->availabilities);

        return view('admins.coach.show', compact('coach'));
    }

    /**
     * Show the form for editing the specified coach.
     */
    public function edit(User $coach)
    {
        // Ensure the user is a coach
        if (!$coach->hasRole('coach')) {
            return redirect()->route('coach.index')
                ->with('error', 'User is not a coach.');
        }

        // Days of the week for availability settings
        $daysOfWeek = [
            'Sunday', 'Monday', 'Tuesday', 'Wednesday',
            'Thursday', 'Friday', 'Saturday'
        ];

        // Get current availability settings
        $availabilities = $coach->availabilities()
            ->orderBy('day_of_week')
            ->get()
            ->groupBy('day_of_week');

        return view('admins.coach.edit', compact('coach', 'daysOfWeek', 'availabilities'));
    }

    /**
     * Update the specified coach in storage.
     */
    public function update(CoachRequest $request, User $coach)
    {
        // Ensure the user is a coach
        if (!$coach->hasRole('coach')) {
            return redirect()->route('coach.index')
                ->with('error', 'User is not a coach.');
        }

        $validated = $request->validated();

        // Update coach data
        $updateData = [
            'name' => $validated['name'],
            'last_name' => $validated['last_name'],
            'display_name' => $validated['display_name'],
            'email' => $validated['email'],
            'whatsapp' => $validated['whatsapp'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ];

        // Only update password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $coach->update($updateData);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo_url')) {
            $destinationPath = 'images/profile-photos';

            // Create directory if it doesn't exist
            if (!File::exists(public_path($destinationPath))) {
                File::makeDirectory(public_path($destinationPath), 0755, true);
            }

            // Delete the old photo if it exists
            if ($coach->profile_photo_url) {
                $oldImagePath = parse_url($coach->profile_photo_url, PHP_URL_PATH);
                $oldImagePath = public_path($oldImagePath);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            $file = $request->file('profile_photo_url');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path($destinationPath), $filename);
            $coach->profile_photo_url = asset($destinationPath . '/' . $filename);
            $coach->save();
        }

        // Update availability settings if provided
        if (isset($validated['availability'])) {
            $this->updateAvailabilitySettings($coach, $validated['availability']);
        }

        // Update blocked times if provided
        if (isset($validated['blocked_times'])) {
            $this->updateBlockedTimes($coach, $validated['blocked_times']);
        }

        return redirect()->route('coach.index')
            ->with('success', 'Coach updated successfully!');
    }

    /**
     * Remove the specified coach from storage.
     */
    public function destroy(User $coach)
    {
        // Ensure the user is a coach
        if (!$coach->hasRole('coach')) {
            return redirect()->route('coach.index')
                ->with('error', 'User is not a coach.');
        }

        // Remove coach role and delete user
        $coach->removeRole('coach');
        $coach->delete();

        return redirect()->route('coach.index')
            ->with('success', 'Coach deleted successfully!');
    }

    /**
     * Create initial availability settings for a coach
     */
    private function createAvailabilitySettings($coach, $availabilityData)
    {
        $daysOfWeek = [
            'Sunday', 'Monday', 'Tuesday', 'Wednesday',
            'Thursday', 'Friday', 'Saturday'
        ];

        foreach ($availabilityData as $dayIndex => $slots) {
            $dayName = $daysOfWeek[$dayIndex]; // 👈 convert number to string
            foreach ($slots as $slot) {
                if (isset($slot['enabled']) && $slot['enabled']) {
                    CoachAvailabilities::create([
                        'coach_id' => $coach->id,
                        'day_of_week' => $dayName,   // now stores "Monday" etc
                        'start_time' => $slot['start_time'],
                        'end_time' => $slot['end_time'],
                        'is_blocked' => false,
                    ]);
                }
            }
        }
    }

    /**
     * Create blocked times for a coach
     */
    private function createBlockedTimes($coach, $blockedData)
    {
        foreach ($blockedData as $blocked) {
            if (isset($blocked['date']) && $blocked['date']) {
                CoachAvailabilities::create([
                    'coach_id' => $coach->id,
                    'blocked_date' => $blocked['date'],
                    'start_time' => $blocked['start_time'] ?? null,
                    'end_time' => $blocked['end_time'] ?? null,
                    'is_blocked' => true,
                    'notes' => $blocked['reason'] ?? null,
                ]);
            }
        }
    }

    /**
     * Update availability settings for a coach
     */
    private function updateAvailabilitySettings($coach, $availabilityData)
    {
        $coach->availabilities()->where('is_blocked', false)->delete();

        $daysOfWeek = [
            'Sunday', 'Monday', 'Tuesday', 'Wednesday',
            'Thursday', 'Friday', 'Saturday'
        ];

        foreach ($availabilityData as $dayIndex => $slots) {
            $dayName = $daysOfWeek[$dayIndex];
            foreach ($slots as $slot) {
                if (isset($slot['enabled']) && $slot['enabled']) {
                    CoachAvailabilities::create([
                        'coach_id' => $coach->id,
                        'day_of_week' => $dayName,
                        'start_time' => $slot['start_time'],
                        'end_time' => $slot['end_time'],
                        'is_blocked' => false,
                    ]);
                }
            }
        }
    }

    /**
     * Update blocked times for a coach
     */
    private function updateBlockedTimes($coach, $blockedData)
    {
        // Delete existing blocked times
        $coach->availabilities()->where('is_blocked', true)->delete();

        // Create new blocked times
        foreach ($blockedData as $blocked) {
            if (isset($blocked['date']) && $blocked['date']) {
                CoachAvailabilities::create([
                    'coach_id' => $coach->id,
                    'blocked_date' => $blocked['date'],
                    'start_time' => $blocked['start_time'] ?? null,
                    'end_time' => $blocked['end_time'] ?? null,
                    'is_blocked' => true,
                    'notes' => $blocked['reason'] ?? null,
                ]);
            }
        }
    }

    /**
     * Get coaches data for DataTables
     */
    public function getCoaches(Request $request)
    {
        if ($request->ajax()) {
            // Get only users with coach role
            $coaches = User::role('coach')->with(['roles', 'availabilities', 'appointmentsAsCoach'])->orderByDESC('id');

            return DataTables::of($coaches)
                ->addIndexColumn()
                ->addColumn('checkbox', function($row) {
                    return '<input type="checkbox" name="coach_id[]" value="'.$row->id.'">';
                })
                ->editColumn('name', function($row) {
                    $avatarUrl = $row->profile_photo_url ?? "https://i.pravatar.cc/40?u=" . urlencode($row->email);
                    return '
                        <div class="d-flex align-items-center">
                            <img src="'.$avatarUrl.'" alt="Avatar" class="user-avatar">
                            <div class="ms-3">
                                <span class="fw-bold">'.$row->name.' '.$row->last_name.'</span>
                                <br>
                                <small class="text-muted">'.$row->display_name.'</small>
                            </div>
                        </div>';
                })
                ->addColumn('contact', function($row) {
                    $contact = '<div>';
                    $contact .= '<div><i class="ti-email"></i> '.$row->email.'</div>';
                    if ($row->whatsapp) {
                        $contact .= '<div><i class="ti-phone"></i> '.$row->whatsapp.'</div>';
                    }
                    $contact .= '</div>';
                    return $contact;
                })
                ->addColumn('appointments', function($row) {
                    $totalAppointments = $row->appointmentsAsCoach->count();
                    $pendingAppointments = $row->appointmentsAsCoach->where('status', 'pending')->count();

                    return '
                        <div>
                            <span class="badge bg-primary">'.$totalAppointments.' Total</span>
                            <span class="badge bg-warning">'.$pendingAppointments.' Pending</span>
                        </div>';
                })
                ->addColumn('availability', function($row) {
                    $availabilityCount = $row->availabilities->count();
                    if ($availabilityCount > 0) {
                        return '<span class="badge bg-success">'.$availabilityCount.' Days Set</span>';
                    }
                    return '<span class="badge bg-danger">Not Set</span>';
                })
                ->addColumn('status', function($row) {
                    $lastActive = $row->updated_at->diffForHumans();
                    return '<span class="text-muted">Active '.$lastActive.'</span>';
                })
                ->addColumn('actions', function($row) {
                    $actions = '
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="'.route('coach.show', $row->id).'">
                                    <i class="ti-eye"></i> View
                                </a></li>

                            </ul>
                        </div>';
                    return $actions;
                })
                ->rawColumns(['checkbox', 'name', 'contact', 'appointments', 'availability', 'actions','status'])
                ->make(true);
        }
    }

    public function updateStatus(Request $request, User $coach)
    {
        $coach->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => 'Approved successfully!'
        ]);
    }
}
//   <li><a class="dropdown-item" href="'.route('coach.edit', $row->id).'">
//                                     <i class="ti-pencil"></i> Edit
//                                 </a></li>
//                                 <li><hr class="dropdown-divider"></li>
//                                 <li>
//                                     <form action="'.route('coach.destroy', $row->id).'" method="POST" class="d-inline">
//                                         '.csrf_field().'
//                                         '.method_field('DELETE').'
//                                         <button type="submit" class="dropdown-item text-danger" onclick="return confirm(\'Are you sure you want to delete this coach?\')">
//                                             <i class="ti-trash"></i> Delete
//                                         </button>
//                                     </form>
//                                 </li>
