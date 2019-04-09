<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function userSelf(Request $request)
    {
        return $request->user();
    }

    public function setLocale(Request $request)
    {
        \App::setLocale('ru');
        $locale = \App::getLocale();
        return $locale;
        // response(['sadas' => \App::isLocale('ru')]);
    }

}
