<?php

use Illuminate\Support\Facades\Route;

Route::get('/players', function () {
    return ['name' => 'John Doe', 'position' => 'Forward'];
});