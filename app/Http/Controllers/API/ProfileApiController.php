<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProfileApiController extends Controller
{
    public function index()
    {
        // Log::info('Profile URL');
        $user = User::with(['profile.pic', 'profile.city'])->find(Auth::id());

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.',
            ], 401);
        }

        if ($user->profile && $user->profile->pic) {
            $user->profile_picture_url = asset(url('/').'/storage/'.$user->profile->pic->path);
        } else {
            $user->profile_picture_url = asset(url('/').'/storage/assets/profile_pic_default.png');
        }

        return response()->json([
            'success' => true,
            'data' => $user,
        ], 200);
    }
}
