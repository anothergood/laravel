<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Localization extends Model
{
    protected $table = 'localization';

    public function localizable()
    {
        return $this->morphTo();
    }
}
