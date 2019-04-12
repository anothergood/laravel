<?php

namespace App\Http\Controllers;

use App\User;
use App\Post;
use App\Comment;
use App\Attachment;
use App\Localization;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Http\Resources\CommentResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreCommentRequest;

class CommentController extends Controller
{
    public function postComments(Request $request, Post $post)
    {
        $friend = $request->user()->approved_friends()->find($post->user_id);

        if ($friend or $post->user_id == $request->user()->id) {
            return CommentResource::collection($post->comments()->paginate(5));
        } else {
            return response(['message' => 'Forbidden'], 403);
        }
    }

    public function sendComment(StoreCommentRequest $request, Post $post)
    {
        $friend = $request->user()->approved_friends()->find($post->user_id);

        if ($friend or $request->user()->id == $post->user_id) {
            $comment = new Comment;
            $comment->user_id = $request->user()->id;
            $comment->post_id = $post->id;
            $comment->save();

            foreach (array_keys($request->body) as $lang) {
                $localization = new Localization;
                $localization->language = $lang;
                $localization->field = 'body';
                $localization->value = $request->body[$lang];
                $comment->localization()->save($localization);
            }

            if ($request->hasFile('file')) {
                $path = Storage::putFile('attachments', new File($request->file), 'public');
                $mime = $request->file->getMimeType();
                $attachment = new Attachment;
                $attachment->file_path = $path;
                $attachment->file_type = $mime;
                $comment->attachments()->save($attachment);
            }
            return new CommentResource($comment);
        } else {
            return response(['message' => 'only friends can post comments'], 403);
        }
    }
}
