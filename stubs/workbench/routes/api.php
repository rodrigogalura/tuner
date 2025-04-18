<?php

use Workbench\App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/api/users', function () {
    return User::send(
        debuggable: true,
        paginatable: true,
    );
});
