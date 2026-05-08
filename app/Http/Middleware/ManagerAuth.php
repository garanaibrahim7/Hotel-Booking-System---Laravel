<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ManagerAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // return $next($request);
        if (! Auth::check()) {
            return redirect()->route('login');
        }
        if (Auth::user()->role == 'manager') {
            return $next($request);
        } else {
            return abort(403, "You Can't Access this Page !");
        }
    }
}
