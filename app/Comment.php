<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['title'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function localization()
    {
        return $this->morphMany(Localization::class, 'localizable');
    }

    public function getFieldAttribute($field = 'body')
    {
        foreach($this->localization as $lang) {
            if ( $lang->field == $field and $lang->language == \App::getLocale() )
                return $lang->value;
        }
    }
}
