<?php

use function Pest\Laravel\get;
use Workbench\App\Models\NoProjectableModel;
use Workbench\App\Models\InvalidProjectableModel;
use Workbench\App\Models\OnlyIdIsProjectableModel;
use Workbench\App\Models\AllFieldsAreProjectableModel;

beforeEach(function () {
    $_GET = [];

    $this->visibleFields = ['id', 'name', 'created_at', 'updated_at'];

    // $this->equivalentRoutes = [
    //     '*' => '/api/all-fields-are-projectable',
    //     'id' => '/api/only-id-is-projectable',
    //     'name' => '/api/only-name-is-projectable',
    //     'id, name' => '/api/id-and-name-are-projectable',
    //     'empty' => '/api/no-projectable',
    // ];
});

describe('Not perform any action. Just return defined value as default.', function () {
    it('should not perform any action if the client input "fields!" is "*"', function () {
        // Prepare
        $_GET['fields!'] = '*';
        $_GET['defined_fields'] = ['id', 'name'];
        $data = AllFieldsAreProjectableModel::factory()->create();

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertJsonCount($data->count())
            ->assertExactJsonStructure(['*' => $_GET['defined_fields']]);
    });

    it('should not perform any action if the projectable field\'s value is empty', function () {
        // Prepare
        $_GET['fields!'] = '*';
        $_GET['defined_fields'] = ['id', 'name'];
        $data = NoProjectableModel::factory()->create();

        // Act & Assert
        get('/api/no-projectable')
            ->assertOk()
            ->assertJsonCount($data->count())
            ->assertExactJsonStructure(['*' => $_GET['defined_fields']]);
    });

    it('should not perform any action if the projectable fields and defined fields are not intersect', function () {
        // Prepare
        $_GET['defined_fields'] = ['name'];
        $data = OnlyIdIsProjectableModel::factory()->create();

        // Act & Assert
        get('/api/only-id-is-projectable')
            ->assertOk()
            ->assertJsonCount($data->count())
            ->assertExactJsonStructure(['*' => $_GET['defined_fields']]);
    });
});

describe('Throw an exception', function () {
    it('should throw an exception if one of projectable fields is invalid', function () {
        $_GET['fields'] = '*';

        // Act & Assert
        get('/api/invalid-projectable')
            ->assertServerError();
    });

    it('should throw an exception if the defined fields is empty', function () {
        $_GET['fields'] = '*';
        $_GET['defined_fields'] = [];

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertServerError();
    });

    it('should throw an exception if one of defined fields is invalid', function () {
        $_GET['fields'] = '*';
        $_GET['defined_fields'] = ['email'];

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertServerError();
    });
});
