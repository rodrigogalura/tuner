<?php

use Workbench\App\Models\User;
use Workbench\App\Models\Phone;
use Illuminate\Support\Facades\Route;
use Workbench\App\Models\NoProjectableModel;
use Workbench\App\Models\OnlyIdIsProjectableModel;
use Workbench\App\Models\AllFieldsAreProjectableModel;

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

Route::any('/api/no-projectable', function() {
    $definedFields = $_GET['defined_fields'] ?? '*';

    return NoProjectableModel::select($definedFields)->send();
});

Route::any('/api/only-id-is-projectable', function() {
    $definedFields = $_GET['defined_fields'] ?? '*';

    return OnlyIdIsProjectableModel::select($definedFields)->send();
});
