<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// routes/web.php
Route::get('/test-assets', function () {
    return response()->json([
        'asset_url' => asset('css/app.css'),
        'public_path' => public_path('css/app.css'),
        'file_exists' => file_exists(public_path('css/app.css')),
        'file_size' => file_exists(public_path('css/app.css'))
            ? filesize(public_path('css/app.css'))
            : 0,
        'last_modified' => file_exists(public_path('css/app.css'))
            ? date('Y-m-d H:i:s', filemtime(public_path('css/app.css')))
            : null,
        'app_url' => config('app.url'),
        'current_url' => url()->current(),
    ]);
});
