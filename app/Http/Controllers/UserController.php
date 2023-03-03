<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

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
            return view('homepage-feed', ['feedPosts' => auth()->user()->feedPosts()->latest()->paginate(5)]);
        } else {
            return view('homepage');
        }
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/')->with('success', 'You have logged out!');
    }

    private function getProfileData($profile)
    {
        $currentlyFollowing = 0;

        if (auth()->check()) {
            $currentlyFollowing = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $profile->id]])->count();
        }


        View::share('sharedData', ['username' => $profile->username,  'postCount' => $profile->posts()->count(), 'avatar' => $profile->avatar, 'currentlyFollowing' => $currentlyFollowing, 'followersCount' => $profile->followers()->count(), 'followingCount' => $profile->following()->count()]);
    }

    public function showProfilePage(User $profile)
    {
        $this->getProfileData($profile);
        return view('profile', ['posts' => $profile->posts()->latest()->get()]);
    }

    public function showAvatarForm()
    {
        return view('avatar-form');
    }

    public function storeAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:3000'
        ]);

        $user = auth()->user();

        $filename = $user->id . '-' . uniqid() . '.jpg';

        $image = Image::make($request->file('avatar'))->fit(120)->encode('jpg');

        Storage::put('public/userAvatars/' . $filename, $image);

        $oldAvatar = $user->avatar;

        $user->avatar = $filename;
        $user->save();

        if ($oldAvatar != '/fallback-avatar.jpg') {
            Storage::delete(str_replace("/storage/", "public/", $oldAvatar));
        }

        return back()->with("success", "New avatar is made!");
    }

    public function showFollowers(User $profile)
    {
        $this->getProfileData($profile);
        return view('profile-followers', ['followers' => $profile->followers()->latest()->get()]);
    }

    public function showFollowing(User $profile)
    {
        $this->getProfileData($profile);
        return view('profile-following', ['following' => $profile->following()->latest()->get()]);
    }
}
