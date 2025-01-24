<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use mpba\Tickets\Models\Setting;

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

Route::get('/', function () {
    if (Auth::guest()) {
        return view('welcome');
    } else {
        $admin_route = Setting::grab('admin_route');

        return redirect(route($admin_route.'.dashboard.indicator'));
    }
});

Auth::routes();

Route::get('/home', function () {
    $admin_route = Setting::grab('admin_route');

    return redirect(route($admin_route.'.dashboard.indicator'));
});
