<?php

use Illuminate\Support\Facades\Route;
use Workbench\App\Models\AllColumnsAreProjectableModel;
use Workbench\App\Models\InvalidProjectableModel;
use Workbench\App\Models\NoProjectableModel;
use Workbench\App\Models\OnlyIdAndNameAreProjectableModel;
use Workbench\App\Models\OnlyIdIsProjectableModel;
use Workbench\App\Models\OnlyNameIsProjectableModel;
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

Route::get('/api/all-columns-are-projectable', function () {
    $definedColumns = $_GET['defined_columns'] ?? '*';

    return AllColumnsAreProjectableModel::select($definedColumns)->send();
});

Route::get('/api/no-projectable', function () {
    $definedColumns = $_GET['defined_columns'] ?? '*';

    return NoProjectableModel::select($definedColumns)->send();
});

Route::get('/api/only-id-is-projectable', function () {
    $definedColumns = $_GET['defined_columns'] ?? '*';

    return OnlyIdIsProjectableModel::select($definedColumns)->send();
});

Route::get('/api/only-name-is-projectable', function () {
    $definedColumns = $_GET['defined_columns'] ?? '*';

    return OnlyNameIsProjectableModel::select($definedColumns)->send();
});

Route::get('/api/only-id-and-name-are-projectable', function () {
    $definedColumns = $_GET['defined_columns'] ?? '*';

    return OnlyIdAndNameAreProjectableModel::select($definedColumns)->send();
});

Route::get('/api/invalid-projectable', function () {
    $definedColumns = $_GET['defined_columns'] ?? '*';

    return InvalidProjectableModel::select($definedColumns)->send();
});
