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

}
