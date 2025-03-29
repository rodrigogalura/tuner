<?php

use Illuminate\Support\Facades\Route;
use Workbench\App\Http\Controllers\UserController;

Route::get('/api/users', [UserController::class, 'index'])->name('api.users.index');
