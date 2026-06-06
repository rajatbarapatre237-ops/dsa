<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/deploy/setup', function () {
    return response()->json((require base_path('bootstrap/deploy-setup.php'))());
});
