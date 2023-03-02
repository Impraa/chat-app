<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    //
    public function createFollow(User $user)
    {
        // You can not follow yourself
        //You can not follow same person twice

        if ($user->id == auth()->user()->id) {
            return back()->with('error', 'You can not follow yourself!');
        }

        $exists = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->count();

        if ($exists) {
            return back()->with('error', 'You are already following that user!');
        }

        $newFollow = new Follow;
        $newFollow->user_id = auth()->user()->id;
        $newFollow->followeduser = $user->id;
        $newFollow->save();
        return back()->with('success', 'You are now following that user!');
    }

    public function removeFollow(User $user)
    {
        Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->delete();
        return back()->with('success', 'You have unfollowed the user successfuly!');
    }
}
