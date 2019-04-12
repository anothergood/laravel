<?php

use Illuminate\Http\Request;
use App\User;

use App\Events\ChatMessage;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::group(['prefix' => 'user','middleware' => 'throttle:5,1'], function () {

    Route::post('new-password', 'ResetPasswordController@newPassword');
    Route::post('reset-password', 'ResetPasswordController@resetPassword');

});

Route::middleware('throttle:60,1')->group(function () {

    Route::group(['prefix' => 'user'], function () {
        Route::post('register', 'RegisterController@store')->name('register');
        Route::post('login', 'LoginController@store')->name('login');
        Route::get('verify/{token}', 'RegisterController@verifyUser');
        Route::get('self', 'UserController@userSelf')->middleware('auth:api')->middleware('verify');
        Route::post('localization', 'UserController@setLocale');
        Route::get('current-language', 'UserController@currentLanguage');
    });

    Route::group(['prefix' => 'friends','middleware' => 'auth:api'], function () {
        Route::post('{user}/invite', 'FriendController@inviteFriend');
        Route::post('{initiator}/approve', 'FriendController@approveFriend');
        Route::get('all', 'FriendController@allFriends');
        Route::get('without-dialog', 'FriendController@withoutDialog');
    });

    Route::group(['prefix' => 'posts','middleware' => 'auth:api'], function () {
        Route::post('create', 'PostController@createPost');
        Route::get('friends', 'PostController@friendsPosts');
        Route::get('my', 'PostController@myPosts');
        Route::get('{post}', 'PostController@getPost');
        Route::post('{post}/comments/send', 'CommentController@sendComment');
        Route::get('{post}/comments', 'CommentController@postComments');
        Route::apiResource('{post}/like', 'LikeController');
    });

    Route::group(['prefix' => 'chat','middleware' => 'auth:api'], function () {
        Route::post('create-chat', 'ChatController@createChat');
        Route::post('{chat}/invite/{user}', 'ChatController@inviteUser');
        Route::get('all', 'ChatController@allChats');
        Route::post('{chat}/send', 'MessageController@sendMessage');
        Route::post('{chat}/all', 'MessageController@allChatMessages');
        Route::post('{chat}/reset-messages', 'MessageController@resetUnreadMessages');
        Route::post('{chat}/invite-chat-list', 'ChatController@inviteChatList');
    });

});

Route::get('chat/send-message', 'ChatController@sendMessage');
