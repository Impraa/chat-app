<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    //
    public function register(Request $request)
    {
        $incomingFields = $request->validate([
            'username' => ['required', 'min:3', 'max:20', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $incomingFields['password'] = bcrypt($incomingFields['password']);

        $newUser = User::create($incomingFields);
        auth()->login($newUser);
        return redirect('/')->with('success', 'You have been successfully registered');
    }

    public function login(Request $request)
    {
        $incomingFields = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required',
        ]);

        if (auth()->attempt(['username' => $incomingFields['loginusername'], 'password' => $incomingFields['loginpassword']])) {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'You are logged in!');
        } else {
            return redirect('/')->with('error', 'Username or Password incorrect');
        }
    }

    public function showCorrectHomepage()
    {
        if (auth()->check()) {
            return view('homepage-feed');
        } else {
            return view('homepage');
        }
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/')->with('success', 'You have logged out!');
    }

    public function showProfilePage(User $profile)
    {
        return view('profile', ['username' => $profile->username, 'posts' => $profile->posts()->latest()->get(), 'postCount' => $profile->posts()->count()]);
    }
}
