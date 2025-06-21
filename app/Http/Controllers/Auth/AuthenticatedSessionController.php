<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Attempt to authenticate using the 'web' guard (for session cookie setup with Sanctum SPA)
        // Ensure that your config/auth.php 'web' guard has 'session' driver and 'users' provider
        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // IMPORTANT FOR SANCTUM SPA:
        // This is typically all that's needed. Sanctum, when configured correctly
        // with EnsureFrontendRequestsAreStateful middleware, will handle
        // setting the session cookie upon successful Auth::attempt.
        // You generally DO NOT return a token here for SPA authentication flow.
        // The frontend will send a GET request to /sanctum/csrf-cookie first,
        // then send the login request, and Laravel will set the session cookie.

        return response()->noContent(); // Return 204 No Content on successful login
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        // For Sanctum SPA, logout invalidates the current session.
        // This effectively logs the user out from the web guard.
        Auth::guard('web')->logout();

        $request->session()->invalidate(); // Invalidate the session
        $request->session()->regenerateToken(); // Regenerate CSRF token

        return response()->noContent(); // Return 204 No Content on successful logout
    }
}