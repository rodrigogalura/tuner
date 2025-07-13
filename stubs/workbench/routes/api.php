<?php

use Workbench\App\Models\User;
use Workbench\App\Models\Phone;
use Illuminate\Support\Facades\Route;
use Workbench\App\Models\NoProjectableModel;
use Workbench\App\Models\InvalidProjectableModel;
use Workbench\App\Models\OnlyIdIsProjectableModel;
use Workbench\App\Models\OnlyNameIsProjectableModel;
use Workbench\App\Models\AllFieldsAreProjectableModel;
use Workbench\App\Models\OnlyIdAndNameAreProjectableModel;

Route::get('/api/users', function () {
    return User::send();
});

Route::get('/api/phones', function () {
    return Phone::send(
        debuggable: true,
        paginatable: true,
    );
});

Route::get('/api/all-fields-are-projectable', function () {
    $definedFields = $_GET['defined_fields'] ?? '*';

    return AllFieldsAreProjectableModel::select($definedFields)->send();
});

Route::get('/api/no-projectable', function() {
    $definedFields = $_GET['defined_fields'] ?? '*';

    return NoProjectableModel::select($definedFields)->send();
});

Route::get('/api/only-id-is-projectable', function() {
    $definedFields = $_GET['defined_fields'] ?? '*';

    return OnlyIdIsProjectableModel::select($definedFields)->send();
});

Route::get('/api/only-name-is-projectable', function() {
    $definedFields = $_GET['defined_fields'] ?? '*';

    return OnlyNameIsProjectableModel::select($definedFields)->send();
});

Route::get('/api/only-id-and-name-are-projectable', function() {
    $definedFields = $_GET['defined_fields'] ?? '*';

    return OnlyIdAndNameAreProjectableModel::select($definedFields)->send();
});

Route::get('/api/invalid-projectable', function() {
    $definedFields = $_GET['defined_fields'] ?? '*';

    return InvalidProjectableModel::select($definedFields)->send();
});
