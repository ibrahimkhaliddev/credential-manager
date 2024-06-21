<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User; // Import the User model
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash; // Import Hash facade for password hashing
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    // use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validate the user's input
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // Create a new User instance
        $user = new User([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => md5($request->input('password')), // Hash the password with MD5 (not recommended for production)
        ]);

        // Save the user to the database
        $user->save();

        // Redirect the user after successful registration
        return redirect()->route('home')->with('success', 'Registration completed successfully!');
    }
}
