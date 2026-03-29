<?php

namespace App\Http\Controllers\Admins\Nutritionist;

use App\Http\Controllers\Controller;
use App\Http\Requests\NutritionistRequest;
use App\Models\User;
use App\Models\CoachAvailabilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class NutritionistController extends Controller
{
    /**
     * Display a listing of nutritionists.
     */
    public function index()
    {
        return view('admins.nutritionist.index');
    }

    /**
     * Show the form for creating a new nutritionist.
     */
    public function create()
    {
        $daysOfWeek = [
            'Sunday', 'Monday', 'Tuesday', 'Wednesday',
            'Thursday', 'Friday', 'Saturday'
        ];

        return view('admins.nutritionist.create', get_defined_vars());
    }

    /**
     * Store a newly created nutritionist in storage.
     */
    public function store(NutritionistRequest $request)
    {
        $validated = $request->validated();

        $nutritionist = User::create([
            'name' => $validated['name'],
            'last_name' => $validated['last_name'],
            'display_name' => $validated['display_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'whatsapp' => $validated['whatsapp'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ]);

        // Assign nutritionist role
        Role::firstOrCreate(['name' => 'nutritionist']);
        $nutritionist->syncRoles(['nutritionist']);

        // Create initial availability and blocked times
        if (isset($validated['availability'])) {
            $this->createAvailabilitySettings($nutritionist, $validated['availability']);
        }

        if (isset($validated['blocked_times'])) {
            $this->createBlockedTimes($nutritionist, $validated['blocked_times']);
        }

        return redirect()->route('nutritionist.index')
            ->with('success', 'Nutritionist created successfully!');
    }

    /**
     * Display the specified nutritionist.
     */
    public function show(User $nutritionist)
    {
        if (!$nutritionist->hasRole('nutritionist')) {
            return redirect()->route('nutritionist.index')
                ->with('error', 'User is not a nutritionist.');
        }

        $nutritionist->load(['availabilities', 'articles', 'appointmentsAsCoach']);

        return view('admins.nutritionist.show', get_defined_vars());
    }

    /**
     * Show the form for editing the specified nutritionist.
     */
    public function edit(User $nutritionist)
    {
        if (!$nutritionist->hasRole('nutritionist')) {
            return redirect()->route('nutritionist.index')
                ->with('error', 'User is not a nutritionist.');
        }

        $daysOfWeek = [
            'Sunday', 'Monday', 'Tuesday', 'Wednesday',
            'Thursday', 'Friday', 'Saturday'
        ];

        $availabilities = $nutritionist->availabilities()
            ->orderBy('day_of_week')
            ->get()
            ->groupBy('day_of_week');

        return view('admins.nutritionist.edit', get_defined_vars());
    }

    /**
     * Update the specified nutritionist in storage.
     */
    public function update(NutritionistRequest $request, User $nutritionist)
    {
        if (!$nutritionist->hasRole('nutritionist')) {
            return redirect()->route('nutritionist.index')
                ->with('error', 'User is not a nutritionist.');
        }

        $validated = $request->validated();

        $updateData = [
            'name' => $validated['name'],
            'last_name' => $validated['last_name'],
            'display_name' => $validated['display_name'],
            'email' => $validated['email'],
            'whatsapp' => $validated['whatsapp'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $nutritionist->update($updateData);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo_url')) {
            $destinationPath = 'images/profile-photos';

            if (!File::exists(public_path($destinationPath))) {
                File::makeDirectory(public_path($destinationPath), 0755, true);
            }

            if ($nutritionist->profile_photo_url) {
                $oldImagePath = parse_url($nutritionist->profile_photo_url, PHP_URL_PATH);
                $oldImagePath = public_path($oldImagePath);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            $file = $request->file('profile_photo_url');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path($destinationPath), $filename);
            $nutritionist->profile_photo_url = asset($destinationPath . '/' . $filename);
            $nutritionist->save();
        }

        // Update availabilities and blocked times
        if (isset($validated['availability'])) {
            $this->updateAvailabilitySettings($nutritionist, $validated['availability']);
        }

        if (isset($validated['blocked_times'])) {
            $this->updateBlockedTimes($nutritionist, $validated['blocked_times']);
        }

        return redirect()->route('nutritionist.index')
            ->with('success', 'Nutritionist updated successfully!');
    }

    /**
     * Remove the specified nutritionist from storage.
     */
    public function destroy(User $nutritionist)
    {
        if (!$nutritionist->hasRole('nutritionist')) {
            return redirect()->route('nutritionist.index')
                ->with('error', 'User is not a nutritionist.');
        }

        $nutritionist->removeRole('nutritionist');
        $nutritionist->delete();

        return redirect()->route('nutritionist.index')
            ->with('success', 'Nutritionist deleted successfully!');
    }

    /**
     * Create availability for a nutritionist.
     */
    // private function createAvailabilitySettings($nutritionist, $availabilityData)
    // {
    //     foreach ($availabilityData as $dayName => $slots) {
    //         foreach ($slots as $slot) {
    //             if (isset($slot['enabled']) && $slot['enabled']) {
    //                 CoachAvailabilities::create([
    //                     'coach_id' => $nutritionist->id,
    //                     'day_of_week' => $dayName,
    //                     'start_time' => $slot['start_time'],
    //                     'end_time' => $slot['end_time'],
    //                     'is_blocked' => false,
    //                 ]);
    //             }
    //         }
    //     }
    // }

    /**
     * Create blocked times for a nutritionist.
     */
    // private function createBlockedTimes($nutritionist, $blockedData)
    // {
    //     foreach ($blockedData as $blocked) {
    //         if (isset($blocked['date']) && $blocked['date']) {
    //             CoachAvailabilities::create([
    //                 'coach_id' => $nutritionist->id,
    //                 'blocked_date' => $blocked['date'],
    //                 'start_time' => $blocked['start_time'] ?? null,
    //                 'end_time' => $blocked['end_time'] ?? null,
    //                 'is_blocked' => true,
    //                 'notes' => $blocked['reason'] ?? null,
    //             ]);
    //         }
    //     }
    // }

    /**
     * Update availability settings for a nutritionist.
     */
    private function updateAvailabilitySettings($nutritionist, $availabilityData)
    {
        $nutritionist->availabilities()->where('is_blocked', false)->delete();

        foreach ($availabilityData as $dayName => $slots) {
            foreach ($slots as $slot) {
                if (isset($slot['enabled']) && $slot['enabled']) {
                    CoachAvailabilities::create([
                        'coach_id' => $nutritionist->id,
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
     * Update blocked times for a nutritionist.
     */
    private function updateBlockedTimes($nutritionist, $blockedData)
    {
        $nutritionist->availabilities()->where('is_blocked', true)->delete();

        foreach ($blockedData as $blocked) {
            if (isset($blocked['date']) && $blocked['date']) {
                CoachAvailabilities::create([
                    'coach_id' => $nutritionist->id,
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
     * Get all nutritionists for DataTables.
     */
    public function getNutritionists(Request $request)
    {
        if ($request->ajax()) {
            $nutritionists = User::role('nutritionist')
                ->with(['roles', 'articles', 'appointmentsAsCoach'])
                ->orderByDesc('id');

            return DataTables::of($nutritionists)
                ->addIndexColumn()
                ->editColumn('name', function ($row) {
                    $avatarUrl = $row->profile_photo_url ?? "https://i.pravatar.cc/40?u=" . urlencode($row->email);
                    return '
                        <div class="d-flex align-items-center">
                            <img src="' . $avatarUrl . '" alt="Avatar" class="user-avatar">
                            <div class="ms-3">
                                <span class="fw-bold">' . $row->name . ' ' . $row->last_name . '</span><br>
                                <small class="text-muted">' . $row->display_name . '</small>
                            </div>
                        </div>';
                })
                ->addColumn('contact', function ($row) {
                    $contact = '<div><i class="ti-email"></i> ' . $row->email . '</div>';
                    if ($row->whatsapp) {
                        $contact .= '<div><i class="ti-phone"></i> ' . $row->whatsapp . '</div>';
                    }
                    return $contact;
                })
                ->addColumn('specialties', function ($row) {
                    return '<span class="text-muted">' . ($row->bio ? Str::limit($row->bio, 50) : 'No specialties listed') . '</span>';
                })
                ->addColumn('content', function ($row) {
                    $articles = $row->articles->count();
                    return '<span class="badge bg-primary">' . $articles . ' Articles</span>';
                })
                ->addColumn('consultations', function ($row) {
                    $total = $row->appointmentsAsCoach->count();
                    $pending = $row->appointmentsAsCoach->where('status', 'pending')->count();
                    return '
                        <div>
                            <span class="badge bg-primary">' . $total . ' Total</span>
                            <span class="badge bg-warning">' . $pending . ' Pending</span>
                        </div>';
                })
                ->addColumn('status', function ($row) {
                    $lastActive = $row->updated_at->diffForHumans();
                    return '<span class="text-muted">Active ' . $lastActive . '</span>';
                })
                ->addColumn('actions', function ($row) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="' . route('nutritionist.show', $row->id) . '"><i class="ti-eye"></i> View</a></li>
                                <li><a class="dropdown-item" href="' . route('nutritionist.edit', $row->id) . '"><i class="ti-pencil"></i> Edit</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="' . route('nutritionist.destroy', $row->id) . '" method="POST" class="d-inline">
                                        ' . csrf_field() . method_field('DELETE') . '
                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm(\'Are you sure you want to delete this nutritionist?\')">
                                            <i class="ti-trash"></i> Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>';
                })
                ->rawColumns(['name', 'contact', 'specialties', 'content', 'consultations', 'status', 'actions'])
                ->make(true);
        }
    }

    // Create availability for a nutritionist
    private function createAvailabilitySettings($nutritionist, $availabilityData)
    {
        foreach ($availabilityData as $dayName => $slots) {
            foreach ($slots as $slot) {
                if (isset($slot['enabled']) && $slot['enabled']) {
                    CoachAvailabilities::create([
                        'coach_id' => $nutritionist->id,
                        'day_of_week' => $dayName,
                        'start_time' => $slot['start_time'],
                        'end_time' => $slot['end_time'],
                        'is_blocked' => false,
                    ]);
                }
            }
        }
    }

    // Create blocked times for a nutritionist
    private function createBlockedTimes($nutritionist, $blockedData)
    {
        foreach ($blockedData as $blocked) {
            if (isset($blocked['date']) && $blocked['date']) {
                CoachAvailabilities::create([
                    'coach_id' => $nutritionist->id,
                    'blocked_date' => $blocked['date'],
                    'start_time' => $blocked['start_time'] ?? null,
                    'end_time' => $blocked['end_time'] ?? null,
                    'is_blocked' => true,
                    'notes' => $blocked['reason'] ?? null,
                ]);
            }
        }
    }
}