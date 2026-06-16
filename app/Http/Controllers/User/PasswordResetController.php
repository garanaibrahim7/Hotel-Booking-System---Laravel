<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    public function sendResetLink(Request $request)
    {
        $email = $request->validate(['email' => 'required|email|exists:users,email']);

        Password::sendResetLink($email);

        return back()->with(['status' => 'Password Reset Link sent Successfully to ' . $email['email'] . ' Check your Mail Inbox']);
    }


    public function changePassword()
    {
        $email = Auth::user()->only('email');

        Password::sendResetLink($email);

        return back()->with(['success' => 'Password Reset Link sent Successfully to ' . $email['email'] . ' Check your Mail Inbox']);
    }



    public function updatePassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:3|confirmed',
            'password_confirmation' => 'required',
        ]);
        $res = Password::reset($request->only(['token', 'email', 'password', 'password_confirmation']), function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        if ($res === Password::PASSWORD_RESET) {
            return back()->with(['success' => 'Password has been Reset Successfully']);
        } else if ($res === Password::InvalidToken) {
            return back()->with(['fail' => 'Invalid Token or Token Expired']);
        }
        return back()->with(['fail' => 'Password reset Token has Expired or Something went Wrong']);
    }
}
