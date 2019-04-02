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
    });

    Route::group(['prefix' => 'friends','middleware' => 'auth:api'], function () {
        Route::post('invite', 'FriendController@inviteFriend');
        Route::post('approve', 'FriendController@approveFriend');
        Route::get('all', 'FriendController@allFriends');
    });

    Route::group(['prefix' => 'posts','middleware' => 'auth:api'], function () {
        Route::apiResource('comments', 'CommentController');
        Route::apiResource('', 'PostController');
        Route::apiResource('like', 'LikeController');
        Route::get('friends-posts', 'PostController@friendsPosts');
        Route::group(['prefix' => 'my-posts'], function () {
            Route::get('', 'PostController@myPosts');
            Route::get('{post}', 'PostController@myPost');
            Route::get('{post}/comments', 'CommentController@myPostComments');
        });
    });

    Route::group(['prefix' => 'messages','middleware' => 'auth:api'], function () {
        Route::post('{chat}/send', 'MessageController@sendMessage');
        Route::post('{user}/received', 'MessageController@receivedFriendMessages');
        Route::post('{user}/sended', 'MessageController@sendedFriendMessages');
        Route::post('{user}/all', 'MessageController@allFriendMessages');
    });
    Route::group(['prefix' => 'chat','middleware' => 'auth:api'], function () {
        Route::post('create-chat', 'ChatController@createChat');
        Route::post('{chat}/invite/{user}', 'ChatController@inviteUser');
        Route::get('all', 'ChatController@allChats');
        Route::post('{chat}/send', 'MessageController@sendMessage');
        Route::post('{chat}/all', 'MessageController@allChatMessages');
        Route::post('{chat}/reset-messages', 'MessageController@resetUnreadMessages');
    });

});

Route::get('chat/send-message', 'ChatController@sendMessage');
Route::post('chat/{chat}', 'MessageController@test')->middleware('auth:api');
