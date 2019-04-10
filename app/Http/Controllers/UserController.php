<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LocalizationRequest;

class UserController extends Controller
{
    public function userSelf(Request $request)
    {
        return $request->user();
    }

    public function setLocale(Request $request)
    {
        // $raw_locale = Session::get('locale');
        //
        // if (in_array($raw_locale, Config::get('app.locales'))) {
        //     $locale = $raw_locale;
        // }
        // else $locale = \Config::get('app.locale');
        //
        // \App::setLocale($locale);
        //
        // return $next($request);
        if (in_array($request->language, \Config::get('app.locales'))) {
            \App::setLocale($request->language);
        }
        // return redirect()->back();
        return redirect()->back(); 
        // return response(['language' => \Config::get('app.locale')]);

        // return  \Config::get('app.locale');
    }

    public function currentLanguage(Request $request)
    {


        return  \Config::get('app.locale');
        // return  response(['language' => \Config::get('app.locale')]);
    }

}
