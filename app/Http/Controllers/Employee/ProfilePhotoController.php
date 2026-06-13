<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfilePhotoController extends Controller
{
    /**
     * Upload / replace profile photo for the logged-in employee.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        $user = $request->user();

        // Delete the old photo from storage if it exists
        if ($user->profile_photo && \Storage::disk('public')->exists($user->profile_photo)) {
            \Storage::disk('public')->delete($user->profile_photo);
        }

        // Store new photo
        $path = $request->file('profile_photo')->store("profile_photos/{$user->id}", 'public');

        $user->profile_photo = $path;
        $user->save();

        return response()->json([
            'success' => true,
            'url'     => asset('storage/' . $path),
            'message' => 'Profile photo updated successfully.',
        ]);
    }

    /**
     * Remove the profile photo.
     */
    public function remove(Request $request)
    {
        $user = $request->user();

        if ($user->profile_photo && \Storage::disk('public')->exists($user->profile_photo)) {
            \Storage::disk('public')->delete($user->profile_photo);
        }

        $user->profile_photo = null;
        $user->save();

        return response()->json(['success' => true]);
    }
}
