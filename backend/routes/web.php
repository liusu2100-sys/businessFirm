<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => '鼠鼠商行 API',
        'docs' => '/api/public/stats',
    ]);
});
