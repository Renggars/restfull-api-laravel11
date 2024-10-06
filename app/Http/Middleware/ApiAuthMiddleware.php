<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = str_replace('Bearer ', '', $request->header('Authorization'));
        // $token = $request->header('Authorization');

        if (!$token) {
            return response()->json([
                'errors' => [
                    'message' => 'Unautorized'
                ]
            ], 401);
        }

        $user = User::where('token', $token)->first();

        if (!$user) {
            return response()->json([
                'errors' => [
                    'message' => 'Invalid token'
                ]
            ], 401);
        }

        Auth::login($user);

        return $next($request);
    }
}
