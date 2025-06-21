<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User; // Make sure your User model path is correct
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // You might want to automatically log in the user after registration
        // For Sanctum SPA authentication, this usually involves creating a token
        // and returning it, or just returning a success response.
        // If you want automatic login, you would typically use:
        // auth()->guard('web')->login($user); // For session-based, usually not for pure API
        // For Sanctum, you would likely return the user or a success message.

        return response()->noContent(); // Or return a JSON success message/user data
    }
}