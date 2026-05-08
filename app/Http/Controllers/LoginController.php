<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:3',
        ]);
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            switch (Auth::user()->role) {
                case 'admin':
                    return redirect()->intended(route('admin.dashboard'));
                    break;
                case 'manager':
                    return redirect()->intended(route('manager.dashboard'));
                    break;
                case 'customer':
                    return redirect()->intended('/');
                    break;
                default:
                    abort(401, 'Undefiened Role');
                    break;
            }
        }

        return back()->withErrors(['password' => 'Invalid Password']);
    }
}
