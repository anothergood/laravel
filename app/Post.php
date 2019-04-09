<?php

namespace App;

use App\Traits\Orderable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Orderable;

    protected $fillable = [
        'title', 'body',
    ];

    public function users()
    {
      return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(User::class, 'user_post','post_id','user_id');
    }

    public function attachments()
    {
         return $this->morphMany(Attachment::class, 'attachable');
    }

    public function localization()
    {
        return $this->morphMany(Localization::class, 'localizable');
    }

    public function getLocalizeField($language, $field){
        return $this->localization()->where(['language' => $language, 'field' => $field])-> firstOrFail()->value;
    }

    public function getLocalizePost($language){
        $this->title = $this->localization()->where(['language' => $language, 'field' => 'title'])-> firstOrFail()->value;
        $this->body = $this->localization()->where(['language' => $language, 'field' => 'body'])-> firstOrFail()->value;
        return $this;
    }

}
