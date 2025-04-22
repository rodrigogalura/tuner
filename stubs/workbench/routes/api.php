<?php

use Illuminate\Support\Facades\Route;
use Workbench\App\Models\User;

Route::get('/api/users', function () {
    return User::send(
        debuggable: true,
        paginatable: true,
    );
});
