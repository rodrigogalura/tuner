<?php

use Illuminate\Support\Facades\Route;
use Workbench\App\Models\Phone;
use Workbench\App\Models\User;

Route::get('/api/users', function () {
    return User::send();
});

Route::get('/api/phones', function () {
    return Phone::send(
        debuggable: true,
        paginatable: true,
    );
});
