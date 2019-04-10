<?php

namespace App\Http\Controllers;

use App\Post;
use App\Attachment;
use App\Localization;
use Illuminate\Http\Request;
use App\Http\Requests\StorePostRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class PostController extends Controller
{
    public function myPosts(Request $request)
    {
        $posts = $request->user()->posts()->paginate(10);
        return $posts;
    }

    public function myPost(Request $request, Post $post)
    {
        if ($post->user_id == $request->user()->id) {
            return response($post);
        } else {
            return response(['message' => 'Forbidden'], 403);
        }
    }

    public function friendsPosts(Request $request)
    {
        $user = $request->user();
        $friend_id = [];
        foreach ($user->users as $friend) {
            $friend_id[] = $friend->id;
        }
        $posts = Post::whereIn('user_id', $friend_id)->paginate(2);
        return response($posts);
    }

    public function store(StorePostRequest $request)
    {
        $post = new Post;

        $post->user_id = $request->user()->id;
        $post->save();

        $localization = new Localization;
        $localization->language = 'ru';
        $localization->field = 'title';
        $localization->value = 'бла';
        $post->localization()->save($localization);

        $localization = new Localization;
        $localization->language = 'ru';
        $localization->field = 'body';
        $localization->value = 'бла-бла-бла';
        $post->localization()->save($localization);

        $localization = new Localization;
        $localization->language = 'en';
        $localization->field = 'title';
        $localization->value = 'bla';
        $post->localization()->save($localization);

        $localization = new Localization;
        $localization->language = 'en';
        $localization->field = 'body';
        $localization->value = 'bla-bla-bla';
        $post->localization()->save($localization);

        if ($request->hasFile('file')) {
            $path = Storage::putFile('attachments', new File($request->file), 'public');
            $mime = $request->file->getMimeType();

            $attachment = new Attachment;
            $attachment->file_path = $path;
            $attachment->file_type = $mime;
            $post->attachments()->save($attachment);

            return response(['post' => $post,
                             'attachment' => $attachment
                           ]);
        }
        return response($post);
    }

}
