<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//
// Route::middleware('auth')->group(function () {
//     Route::get('start', function () {
//       return view('start');
//     });
// });

// Route::get('welcome', function () {
//     return view('welcome');
// });

// Route::get('login', function () {
//     return view('login');
// });

Route::get('{any}', function () {
  return view('start');
})->where('any', '.*');

// Route::get('chat', function () {
//     return view('chat');
// });
