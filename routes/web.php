<?php

use App\Events\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FollowController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/admins-only', function () {
    return "you are admin congrats";
})->middleware('can:visitAdminPages');

Route::get('/', [UserController::class, "showCorrectHomepage"])->name('login');
Route::post('/register', [UserController::class, "register"])->middleware('guest');
Route::post('/login', [UserController::class, "login"])->middleware('guest');
Route::post('/logout', [UserController::class, "logout"])->middleware('mustBeLoggedIn');
Route::get('/manage-avatar', [UserController::class, 'showAvatarForm'])->middleware('mustBeLoggedIn');
Route::post('/manage-avatar', [UserController::class, 'storeAvatar'])->middleware('mustBeLoggedIn');

Route::get('/create-post', [PostController::class, 'showCreatePost'])->middleware('mustBeLoggedIn');
Route::post('/create-post', [PostController::class, 'storeNewPost'])->middleware('mustBeLoggedIn');
Route::get('/post/{post}', [PostController::class, 'showPost']);
Route::delete('/post/{post}', [PostController::class, 'deletePost'])->middleware('can:delete,post');
Route::get('/post/{post}/edit', [PostController::class, 'showUpdatePost'])->middleware('can:update,post');
Route::put('/post/{post}', [PostController::class, 'updatePost']);
Route::get('/search/{term}', [PostController::class, 'search']);

Route::post('/create-follow/{user:username}', [FollowController::class, 'createFollow'])->middleware('mustBeLoggedIn');
Route::post('/remove-follow/{user:username}', [FollowController::class, 'removeFollow'])->middleware('mustBeLoggedIn');

Route::get('/profile/{profile:username}', [UserController::class, 'showProfilePage'])->middleware('mustBeLoggedIn');
Route::get('/profile/{profile:username}/followers', [UserController::class, 'showFollowers'])->middleware('mustBeLoggedIn');
Route::get('/profile/{profile:username}/following', [UserController::class, 'showFollowing'])->middleware('mustBeLoggedIn');

Route::middleware('cache.headers:public;max_age=20;etag')->group(function () {
    Route::get('/profile/{profile:username}/raw', [UserController::class, 'profileRaw'])->middleware('mustBeLoggedIn');
    Route::get('/profile/{profile:username}/followers/raw', [UserController::class, 'followersRaw'])->middleware('mustBeLoggedIn');
    Route::get('/profile/{profile:username}/following/raw', [UserController::class, 'followingRaw'])->middleware('mustBeLoggedIn');
});

Route::post('/send-chat-message', function (Request $request) {
    $incomingFields = $request->validate([
        'textvalue' => 'required'
    ]);

    if (!trim(strip_tags($incomingFields['textvalue']))) {
        return response()->noContent();
    }

    broadcast(new Message(['username' => auth()->user()->username, 'textvalue' => strip_tags($request->textvalue), 'avatar' => auth()->user()->avatar]))->toOthers();

    return response()->noContent();
})->middleware('mustBeLoggedIn');
