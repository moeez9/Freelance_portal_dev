<?php

namespace App\Http\Controllers;

use App\Models\AdminProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminProfileController extends Controller
{
    public function setupForm(Request $request)
    {
        $email = (string) $request->session()->get('admin_auth.email');
        $profile = AdminProfile::where('email', $email)->first();

        return view('admin.profile.setup', compact('email', 'profile'));
    }

    public function saveSetup(Request $request)
    {
        $email = (string) $request->session()->get('admin_auth.email');
        if ($email === '') {
            return redirect()->route('admin.login');
        }

        $profile = AdminProfile::firstOrNew(['email' => $email]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'profile_pic' => [$profile->exists ? 'nullable' : 'required', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('profile_pic')) {
            if ($profile->profile_pic && Storage::disk('public')->exists($profile->profile_pic)) {
                Storage::disk('public')->delete($profile->profile_pic);
            }
            $profile->profile_pic = $request->file('profile_pic')->store('admins/profiles', 'public');
        }

        $profile->name = $validated['name'];
        $profile->save();

        return redirect()->route('admin.dashboard')->with('success', 'Admin profile saved.');
    }
}
