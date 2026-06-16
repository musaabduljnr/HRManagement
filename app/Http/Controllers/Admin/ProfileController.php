<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Modules\Pim\Repositories\Interfaces\EmployeeRepositoryInterface as EmployeeRepository;

class ProfileController extends Controller
{
    /**
     * Show the profile config page.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        return view('profile', compact('user'));
    }

    /**
     * Store profile details.
     */
    public function store(ProfileRequest $request, EmployeeRepository $employeeRepository)
    {
        $employeeRepository->update($request->user()->id, $request->all());

        $request->session()->flash('success', trans('app.profile.update_success'));
        return redirect()->route('profile.index');
    }

    /**
     * Upload / replace profile photo.
     */
    public function uploadPhoto(Request $request)
    {
        $this->validate($request, [
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        $user = $request->user();

        // Delete the old photo from storage if it exists
        if ($user->profile_photo && \Storage::disk('public')->exists($user->profile_photo)) {
            \Storage::disk('public')->delete($user->profile_photo);
        }

        // Store new photo under profile_photos/{user_id}/
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
    public function removePhoto(Request $request)
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
