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
    public function myPostComments(Request $request, Post $post)
    {
        if ($post->user_id == $request->user()->id) {
            return CommentResource::collection($post->comments()->paginate(5));
        } else {
            return response(['message' => 'Forbidden'], 403);
        }
    }

    public function sendComment(StoreCommentRequest $request)
    {
        $post_user = Post::find($request->post_id);

        $friend = $request->user()->approved_friends()->find($post_user->user_id);

        if ($friend or $request->user()->id == $post_user->user_id) {
            $comment = new Comment;
            $comment->user_id = $request->user()->id;
            $comment->post_id = $request->post_id;
            $comment->save();

            $localization = new Localization;
            $localization->language = 'ru';
            $localization->field = 'body';
            $localization->value = 'привет';
            $comment->localization()->save($localization);

            $localization = new Localization;
            $localization->language = 'en';
            $localization->field = 'body';
            $localization->value = 'hello';
            $comment->localization()->save($localization);

            if ($request->hasFile('file')) {
                $path = Storage::putFile('attachments', new File($request->file), 'public');
                $mime = $request->file->getMimeType();
                $attachment = new Attachment;
                $attachment->file_path = $path;
                $attachment->file_type = $mime;
                $comment->attachments()->save($attachment);
                return response(['comment' => $comment,
                                 'attachment' => $attachment]);
            }
            return response($comment);
        } else {
            return response(['message' => 'only friends can post comments'], 403);
        }
    }
}
