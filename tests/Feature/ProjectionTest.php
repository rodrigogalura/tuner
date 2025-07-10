<?php

use Workbench\App\Models\User;

use function Pest\Laravel\get;

beforeEach(function () {
    $_GET = [];
});

test('basic', function () {
    // Prepare
    $_GET['fields'] = 'id,name,email';

    // Act
    User::send();

    // Assert
    get('/api/users')
        ->assertOk()
        ->assertJsonStructure([
            '*' => ['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at'],
        ]);
});
