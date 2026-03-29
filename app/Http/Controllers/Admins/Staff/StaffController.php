<?php

namespace App\Http\Controllers\Admins\Staff;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StaffRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

class StaffController extends Controller
{
    public function index(){
        $roles = Role::all();
        return view('admins.staff.index', compact('roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admins.staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,suspended',
            'password' => 'required|string|min:8|confirmed',
            'last_name' => 'nullable|string|max:255',
            'display_name' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'profile_photo_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Create user
        $staff = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'last_name' => $validatedData['last_name'] ?? null,
            'display_name' => $validatedData['display_name'] ?? null,
            'whatsapp' => $validatedData['whatsapp'] ?? null,
            'email_verified_at' => $validatedData['status'] === 'active' ? now() : null,
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo_url')) {
            $destinationPath = 'images/profile-photos';

            // Create directory if it doesn't exist
            if (!File::exists(public_path($destinationPath))) {
                File::makeDirectory(public_path($destinationPath), 0755, true);
            }

            $file = $request->file('profile_photo_url');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path($destinationPath), $filename);
            $staff->profile_photo_url = asset($destinationPath . '/' . $filename);
            $staff->save();
        }

        // Assign role
        $staff->assignRole($validatedData['role']);

        return redirect()->route('staff.index')
            ->with('success', 'User created successfully!');
    }

    public function show(User $staff){
        $staff->load(['roles', 'subscriptions', 'profile', 'orders']);
        return view('admins.staff.show', compact('staff'));
    }

    public function edit(User $staff){
        $roles = Role::all();
        return view('admins.staff.edit', compact('staff', 'roles'));
    }

    public function update(Request $request, User $staff){
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $staff->id,

            'last_name' => 'nullable|string|max:255',
            'display_name' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'profile_photo_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Remove profile_photo_url from validated data as it needs special handling
        $profileData = $validatedData;
        unset($profileData['profile_photo_url']);

        $staff->update($profileData);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo_url')) {
            $destinationPath = 'images/profile-photos';

            // Create directory if it doesn't exist
            if (!File::exists(public_path($destinationPath))) {
                File::makeDirectory(public_path($destinationPath), 0755, true);
            }

            // Delete the old photo if it exists
            if ($staff->profile_photo_url) {
                $oldImagePath = parse_url($staff->profile_photo_url, PHP_URL_PATH);
                $oldImagePath = public_path($oldImagePath);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            $file = $request->file('profile_photo_url');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path($destinationPath), $filename);
            $staff->profile_photo_url = asset($destinationPath . '/' . $filename);
            $staff->save();
        }

        // Update role
        $staff->syncRoles([$request['role']]);

        // Handle suspension
        if ($request['status'] === 'suspended') {
            $staff->update(['email_verified_at' => null]);
        } elseif ($request['status'] === 'active' && !$staff->email_verified_at) {
            $staff->update(['email_verified_at' => now()]);
        }

        return redirect()->route('staff.index')
            ->with('success', 'User updated successfully!');
    }

    public function destroy(User $staff){
        if ($staff->id === auth()->id()) {
            return redirect()->route('staff.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $staff->delete();

        return response()->json([
            'success' => true,
            'message' => "Deleted successfully!",
        ]);
        return redirect()->route('staff.index')
            ->with('success', 'User deleted successfully!');
    }

    public function resetPassword(Request $request, $id){
        // Generate a new password
        $request->validate([
            'password' => 'required|string|min:8',
        ]);
        $staff = User::findOrFail($id);
        $newPassword = $request->password;
        $staff->update([
            'password' => Hash::make($newPassword)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset successfully.'
        ]);
    }

    public function toggleStatus(User $staff){
        $newStatus = $staff->email_verified_at ? null : now();
        $staff->update(['email_verified_at' => $newStatus]);

        $status = $newStatus ? 'activated' : 'suspended';
        return response()->json([
            'success' => true,
            'message' => "User {$status} successfully!",
            'status' => $status
        ]);
    }

    // public function getUsers(Request $request)
    // {
    //     if ($request->ajax()) {
    //         // Start the query, eager loading relationships for performance
    //         $data = User::with(['roles', 'subscriptions'])->select('users.*')->orderByDesc('users.id');

    //         // --- FILTERING LOGIC ---

    //         // 1. Filter by Role
    //         if ($request->filled('role')) {
    //             $roleId = $request->input('role');
    //             $data->whereHas('roles', function ($query) use ($roleId) {
    //                 $query->where('id', $roleId);
    //             });

    //         }

    //         // 2. Filter by Subscription Status
    //         if ($request->filled('subscription')) {
    //             $status = $request->input('subscription');
    //             if ($status === 'active') {
    //                 // Find users who have at least one active subscription
    //                 $data->whereHas('subscriptions', function ($query) {
    //                     $query->where('status', 'active');
    //                 });
    //             } elseif ($status === 'inactive') {
    //                 // Find users who DO NOT have any active subscriptions.
    //                 // This covers users with only inactive subscriptions AND users with no subscriptions at all.
    //                 $data->whereDoesntHave('subscriptions', function ($query) {
    //                     $query->where('status', 'active');
    //                 });
    //             }
    //         }

    //         return DataTables::of($data)
    //             ->addIndexColumn()
    //             ->addColumn('checkbox', function($row){
    //                 // Important: Use $row->id for the value
    //                 return '<input type="checkbox" class="user-checkbox" name="user_id[]" value="'.$row->id.'">';
    //             })
    //              ->filterColumn('name', function($query, $keyword) {
    //                     $query->whereRaw("LOWER(name) like ?", ["%".strtolower($keyword)."%"]);
    //             })
    //              ->filterColumn('email', function($query, $keyword) {
    //                     $query->whereRaw("LOWER(email) like ?", ["%".strtolower($keyword)."%"]);
    //             })
    //             ->editColumn('name', function($row){
    //                 $avatarUrl = $row->profile_photo_url ? $row->profile_photo_url : "https://i.pravatar.cc/40?u=" . urlencode($row->email);
    //                 return '
    //                     <div class="d-flex align-items-center">
    //                         <img src="'.$avatarUrl.'" alt="Avatar" class="user-avatar">
    //                         <span>'.$row->name.'</span>
    //                     </div>';
    //             })
    //             ->editColumn('role', function($row){

    //                 if ($row->roles->isNotEmpty()) {
    //                     $badges = $row->roles->map(function ($role) {
    //                         $roleName = ucfirst($role->name);
    //                         // if ($roleName == 'admin') {
    //                         //     return '<span class="badge-custom badge-purple">'.$roleName.'</span>';
    //                         // }

    //                         return '<span class="badge-custom badge-secondary">'.$roleName.'</span>';
    //                     });

    //                     return $badges->implode(' ');
    //                 }

    //                 return 'No Role';
    //             })
    //             ->addColumn('subscription', function($row){
    //                 // This logic remains the same, as it's for display, not filtering.
    //                 $hasActiveSubscription = $row->subscriptions->where('status', 'active')->isNotEmpty();

    //                 if ($hasActiveSubscription) {
    //                     return '<span class="badge-custom badge-green">Active</span>';
    //                 }
    //                 return '<span class="badge-custom badge-danger">Inactive</span>';
    //             })
    //             ->editColumn('last_active', function($row){
    //                 return $row->last_active ? $row->last_active->format('Y-m-d h:i A') : 'N/A';
    //             })
    //             ->addColumn('actions', function($row){
    //                 // Updated to use proper dropdown with arrow icon
    //                 $actions = '
    //                     <div class="dropdown">
    //                         <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
    //                             Actions
    //                         </button>
    //                         <ul class="dropdown-menu">
    //                             <li><a class="dropdown-item" href="'.route('staff.show', $row->id).'">
    //                                 <i class="ti-eye"></i> View
    //                             </a></li>
    //                             <li><a class="dropdown-item" href="'.route('staff.edit', $row->id).'">
    //                                 <i class="ti-pencil"></i> Edit
    //                             </a></li>

    //                         </ul>
    //                     </div>';
    //                 return $actions;
    //             })
    //             ->rawColumns(['checkbox', 'name', 'role', 'subscription', 'actions'])
    //             ->make(true);
    //         }
    // }

    public function getUsers(Request $request)
    {
        $data = User::with(['roles', 'subscriptions'])
            ->select('users.*')
            ->orderByDesc('users.id');

        // Role filter (optional)
        $role = $request->query('role');
        if ($role === 'customer') {
            $data->whereHas('roles', fn($q) => $q->where('id', (int)$role));
        }

        // Subscription filter (optional)
        $sub = $request->query('subscription');
        if ($sub === 'active') {
            $data->whereHas('subscriptions', fn($q) => $q->where('status', 'active'));
        } elseif ($sub === 'inactive') {
            $data->whereDoesntHave('subscriptions', fn($q) => $q->where('status', 'active'));
        }

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('checkbox', fn($row) =>
                '<input type="checkbox" class="user-checkbox" name="user_id[]" value="'.$row->id.'">'
            )
            ->filterColumn('name', fn($q,$k) =>
                $q->whereRaw("LOWER(name) like ?", ["%".strtolower($k)."%"])
            )
            ->filterColumn('email', fn($q,$k) =>
                $q->whereRaw("LOWER(email) like ?", ["%".strtolower($k)."%"])
            )
            ->editColumn('name', function($row){
                $avatarUrl = $row->profile_photo_url ?: "https://i.pravatar.cc/40?u=" . urlencode($row->email);
                return '<div class="d-flex align-items-center">
                            <img src="'.$avatarUrl.'" alt="Avatar" class="user-avatar">
                            <span>'.$row->name.'</span>
                        </div>';
            })
            ->editColumn('role', function($row){
                if ($row->roles->isNotEmpty()) {
                    return $row->roles->map(fn($role) =>
                        '<span class="badge-custom badge-secondary">'.ucfirst($role->name).'</span>'
                    )->implode(' ');
                }
                return 'No Role';
            })
            ->addColumn('subscription', function($row){
                $hasActive = $row->subscriptions->where('status', 'active')->isNotEmpty();
                return $hasActive
                    ? '<span class="badge-custom badge-green">Active</span>'
                    : '<span class="badge-custom badge-danger">Inactive</span>';
            })
            ->editColumn('last_active', fn($row) =>
                $row->last_active ? $row->last_active->format('Y-m-d h:i A') : 'N/A'
            )
            ->addColumn('actions', function($row){
                return '<div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="'.route('staff.show', $row->id).'"><i class="ti-eye"></i> View</a></li>
                                <li><a class="dropdown-item" href="'.route('staff.edit', $row->id).'"><i class="ti-pencil"></i> Edit</a></li>
                            </ul>
                        </div>';
            })
            ->rawColumns(['checkbox','name','role','subscription','actions'])
            ->make(true);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No users selected.'], 400);
        }

        // Prevent deleting yourself
        $ids = array_diff($ids, [auth()->id()]);

        User::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Selected users deleted successfully.'
        ]);
    }

    public function bulkExport(Request $request)
    {
        $ids = explode(',', $request->query('ids', ''));

        if (empty($ids)) {
            return redirect()->back()->with('error', 'No users selected for export.');
        }

        $users = User::whereIn('id', $ids)->get(['id', 'name', 'email', 'created_at']);

        $csvFileName = 'users_export_' . now()->format('Y_m_d_His') . '.csv';

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['ID', 'Name', 'Email', 'Created At']);

        foreach ($users as $user) {
            fputcsv($handle, [$user->id, $user->name, $user->email, $user->created_at]);
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$csvFileName}"
        ]);
    }
}
