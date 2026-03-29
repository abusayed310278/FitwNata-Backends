<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('admins.profile.index', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    // public function updateProfile(Request $request, User $user)
    // {

    //     $validatedData = $request->validate([
    //         'name' => ['required', 'string', 'max:255'],
    //         'last_name' => ['required', 'string', 'max:255'],
    //         'display_name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
    //         // 'role' => ['required', 'string', Rule::in(['subscriber', 'editor', 'admin'])],
    //         'whatsapp' => ['nullable', 'string', 'max:255'],
    //         'bio' => ['nullable', 'string', 'max:400'],
    //         'profile_photo_url' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'], // 2MB Max
    //     ]);

    //     // Remove profile_photo_url from validated data as it needs special handling
    //     $profileData = $validatedData;
    //     unset($profileData['profile_photo_url']);

    //     $user->update($profileData);

    //     if ($request->hasFile('profile_photo_url')) {
    //         $destinationPath = 'images/profile-photos';

    //         // Create directory if it doesn't exist
    //         if (!File::exists(public_path($destinationPath))) {
    //             File::makeDirectory(public_path($destinationPath), 0755, true);
    //         }

    //         // Delete the old photo if it exists
    //         if ($user->profile_photo_url) {
    //             $oldImagePath = parse_url($user->profile_photo_url, PHP_URL_PATH);
    //             $oldImagePath = public_path($oldImagePath);
    //             if (File::exists($oldImagePath)) {
    //                 File::delete($oldImagePath);
    //             }
    //         }

    //         $file = $request->file('profile_photo_url');
    //         $filename = time() . '_' . $file->getClientOriginalName();
    //         $file->move(public_path($destinationPath), $filename);
    //         $user->profile_photo_url = asset($destinationPath . '/' . $filename);
    //         $user->save();
    //     }

    //     return redirect()->back()->with('success', 'Profile updated successfully!');
    // }

    public function updateProfile(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'display_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:400'],
        ]);

        // Remove image field for now — we'll handle it separately
        $profileData = $validatedData;

        // Update basic profile info

        // Handle profile image upload
        if ($request->profile_photo_url) 
        {
            $request->validate([
                'profile_photo_url' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            ]);
            $uploadedPath = uploadImage($request->profile_photo_url, 'images/profile-photos');
        }

        $profileData['profile_photo_url'] = $uploadedPath ?? $user->profile_photo_url;
        
        $user->update($profileData);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }


    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request, User $user)
    {
        $validatedData = $request->validate([
            // 'current_password' is a built-in Laravel rule to check against the DB
            // We use 'old_password' as the field name to match the form
            'old_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', Password::min(8)->mixedCase()->numbers(), 'different:old_password'],
        ]);

        $user->update([
            'password' => Hash::make($validatedData['new_password']),
        ]);

        return redirect()->back()->with('success', 'Password changed successfully!');
    }
}
