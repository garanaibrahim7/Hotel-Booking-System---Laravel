<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Str;

class UserProfileController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|min:2|max:255',
            'email'    => 'required|email:strict|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone'    => 'required|string|min:10|max:15',
        ]);
        $user = User::create($validated);
        Auth::login($user);
        return redirect()->intended();
    }

    public function profile()
    {
        $user = User::findOrFail(Auth::user()->id);
        $userProfile = $user->profile;

        return view('client.profile', compact('user', 'userProfile'));
    }

    public function updateProfile(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'required',
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date|before:today',
            'id_type' => 'nullable|required_with:id_number|string',
            'id_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city_id' => 'nullable|exists:cities,id',
            'pincode' => 'nullable|string',
            'profile_pic' => 'nullable|image|max:10240',
        ]);

        $user = Auth::user();

        $user->update($request->only(['name', 'phone']));

        $profile = $user->profile()->updateOrCreate(
            ['user_id' => Auth::id()],
            $request->only([
                'gender',
                'dob',
                'id_type',
                'id_number',
                'address',
                'city_id',
                'pincode'
            ])
        );

        if ($request->hasFile('profile_pic')) {

            $file = $request->file('profile_pic');
            $path = $file->store('assets', 'public');

            if ($profile->pic) {
                Storage::disk('public')->delete($profile->pic->path);
            }

            $profile->pic()->updateOrCreate(
                [],
                ['path' => $path]
            );
        }

        return redirect()->route('client.profile');

        return $validated;
    }

    public function destroyAccount(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'password' => 'required',
            'confirm_deletion' => 'required'
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'The provided password does not match our records.']);
        }

        if ($user->profile && $user->profile->profile_pic) {
            Storage::disk('public')->delete($user->profile->profile_pic);
        }

        $user->profile?->update([
            'address' => null,
            'gender' => null,
            'dob' => null,
            'address' => null,
            'city_id' => null,
            'pincode' => null,
            'id_type' => null,
            'id_number' => null,
        ]);

        Auth::logout();
        $user->update([
            'name' => Str::random(5),
            'email' => Str::random(5) . '@deleted.user',
            'password' => \Hash::make(Str::random(5)),
            'phone' => rand(),
            'role' => 'customer',
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.home')->with('success', 'Your account and personal data have been Completly Deleted as per GDPR regulations.');
    }
}
