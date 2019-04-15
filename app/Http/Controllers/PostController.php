<?php

namespace App\Http\Controllers;

use App\Post;
use App\Attachment;
use App\Localization;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Http\Requests\StorePostRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PostCollection;

use Illuminate\Support\Arr;

class PostController extends Controller
{
    public function myPosts(Request $request)
    {
        return PostResource::collection(Post::where('user_id', $request->user()->id)->whereHas('localization', function ($query) {
            $query->where('language', app()->getLocale());
        })->paginate(5));
    }

    public function friendsPosts(Request $request)
    {
        $friends = $request->user()->approved_friends()->pluck('id');
        return PostResource::collection(Post::whereIn('user_id', $friends)->paginate(5));
    }

    public function getPost(Request $request, Post $post)
    {
        $friend = $request->user()->approved_friends()->find($post->user_id);

        if ($friend or $post->user_id == $request->user()->id) {
            return new PostResource($post);
        } else {
            return response(['message' => 'Forbidden'], 403);
        }
    }

    public function createPost(StorePostRequest $request)
    {
        $post = new Post;
        $post->user_id = $request->user()->id;
        $post->save();

        foreach ($request->titles as $key => $title) {
            $localization = new Localization;
            $localization->language = $key;
            $localization->field = 'title';
            $localization->value = $title;
            $post->localization()->save($localization);
        }

        foreach ($request->bodies as $key => $body) {
            $localization = new Localization;
            $localization->language = $key;
            $localization->field = 'body';
            $localization->value = $body;
            $post->localization()->save($localization);
        }

        if ($request->hasFile('file')) {
            $path = Storage::putFile('attachments', new File($request->file), 'public');
            $mime = $request->file->getMimeType();

            $attachment = new Attachment;
            $attachment->file_path = $path;
            $attachment->file_type = $mime;
            $post->attachments()->save($attachment);

        }
        return new PostResource($post);
    }

}
