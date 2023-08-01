<?php

namespace App\Http\Middleware;

use App\Models\Attendee;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AttendeeAPIToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->input('token');

        if(!$token) {
            return \response()->json([
                'message' => 'Invalid token'
            ], 401);
        }

        $attendee = Attendee::query()->where('login_token', $token)->first();

        if(!$attendee) {
            return \response()->json([
                'message' => 'Invalid token'
            ], 401);
        }

        Auth::setUser($attendee);

        return $next($request);
    }
}
