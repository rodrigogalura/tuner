<?php

use Workbench\App\Models\AllFieldsAreProjectableModel;
use Workbench\App\Models\NoProjectableModel;
use Workbench\App\Models\OnlyIdIsProjectableModel;

use function Pest\Laravel\get;

beforeEach(function (): void {
    $_GET = [];

    $this->data = [
        ['id' => 1, 'name' => 'Mr. Anderson'],
        ['id' => 2, 'name' => 'John Wick'],
        ['id' => 3, 'name' => 'Peter Parker SR.'],
        ['id' => 4, 'name' => 'John Doe JR.'],
        ['id' => 5, 'name' => 'Foo Bar III'],
    ];
});

describe('Not perform any action.', function (): void {
    it('should not perform any action if the search fields is empty', function (): void {
        // Prepare
        $_GET['search'] = ['' => 'Mr%'];
        AllFieldsAreProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertExactJson($this->data);
    });

    it('should not perform any action if the search fields is invalid', function (): void {
        // Prepare
        $_GET['search'] = ['email' => 'Mr%']; // email is non-exist column
        AllFieldsAreProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertExactJson($this->data);
    });

    it('should not perform any action if the search value is empty', function (): void {
        // Prepare
        $_GET['search'] = ['name' => ''];
        AllFieldsAreProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertExactJson($this->data);
    });

    it('should not perform any action if the search value is not hit the minimum', function (): void {
        $minimumLength = config('tweaker.searching.minimum_length');

        // Prepare
        $_GET['search'] = ['name' => str_repeat('a', $minimumLength - 1)];
        AllFieldsAreProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertExactJson($this->data);
    });

    it('should not perform any action if the searchable fields are empty', function (): void {
        // Prepare
        $_GET['search'] = ['name' => 'Mr%'];
        NoProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/no-projectable')
            ->assertOk()
            ->assertExactJson($this->data);
    });

    it('should not perform any action if the result key is empty', function (): void {
        // Prepare
        $_GET['search'] = ['name' => 'Mr%'];
        OnlyIdIsProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/only-id-is-projectable')
            ->assertOk()
            ->assertExactJson($this->data);
    });
});

// describe('Throw an exception', function (): void {
//     it('should throw an exception if one of projectable fields is invalid', function (): void {
//         $_GET['fields'] = '*';

//         // Act & Assert
//         get('/api/invalid-projectable')
//             ->assertServerError();
//     });

//     it('should throw an exception if the defined fields is empty', function (): void {
//         $_GET['fields'] = '*';
//         $_GET['defined_fields'] = [];

//         // Act & Assert
//         get('/api/all-fields-are-projectable')
//             ->assertServerError();
//     });

//     it('should throw an exception if one of defined fields is invalid', function (): void {
//         $_GET['fields'] = '*';
//         $_GET['defined_fields'] = ['email'];

//         // Act & Assert
//         get('/api/all-fields-are-projectable')
//             ->assertServerError();
//     });
// });

// describe('Valid scenarios', function (): void {
//     it('should passed all valid scenarios for client input "fields"', function ($projectableFields, $definedFields, $clientInput, $expectedResult): void {
//         // Prepare
//         $equivalentRoutes = [
//             '*' => '/api/all-fields-are-projectable',
//             'id' => '/api/only-id-is-projectable',
//             'name' => '/api/only-name-is-projectable',
//             'id, name' => '/api/only-id-and-name-are-projectable',
//             'empty' => '/api/no-projectable',
//         ];

//         $models = [
//             '*' => AllFieldsAreProjectableModel::class,
//             'id' => OnlyIdIsProjectableModel::class,
//             'name' => OnlyNameIsProjectableModel::class,
//             'id, name' => OnlyIdAndNameAreProjectableModel::class,
//             'empty' => NoProjectableModel::class,
//         ];

//         $key = implode(', ', $projectableFields);

//         $_GET['fields'] = $clientInput;
//         $_GET['defined_fields'] = $definedFields;

//         $model = $models[$key];
//         $data = $model::factory(rand(2, 5))->create();

//         $route = $equivalentRoutes[$key];

//         // Act & Assert
//         get($route)
//             ->assertOk()
//             ->assertJsonCount(empty($expectedResult) ? 0 : $data->count())
//             ->assertExactJsonStructure(['*' => $expectedResult]);
//     })
//         ->with('fields-truth-table');

//     it('should passed all valid scenarios for client input "fields!"', function ($projectableFields, $definedFields, $clientInput, $expectedResult): void {
//         // Prepare
//         $equivalentRoutes = [
//             '*' => '/api/all-fields-are-projectable',
//             'id' => '/api/only-id-is-projectable',
//             'name' => '/api/only-name-is-projectable',
//             'id, name' => '/api/only-id-and-name-are-projectable',
//             'empty' => '/api/no-projectable',
//         ];

//         $models = [
//             '*' => AllFieldsAreProjectableModel::class,
//             'id' => OnlyIdIsProjectableModel::class,
//             'name' => OnlyNameIsProjectableModel::class,
//             'id, name' => OnlyIdAndNameAreProjectableModel::class,
//             'empty' => NoProjectableModel::class,
//         ];

//         $key = implode(', ', $projectableFields);

//         $_GET['fields!'] = $clientInput;
//         $_GET['defined_fields'] = $definedFields;

//         $model = $models[$key];
//         $data = $model::factory(rand(2, 5))->create();

//         $route = $equivalentRoutes[$key];

//         // Act & Assert
//         get($route)
//             ->assertOk()
//             ->assertJsonCount(empty($expectedResult) ? 0 : $data->count())
//             ->assertExactJsonStructure(['*' => $expectedResult]);
//     })
//         ->with('fields-not-truth-table');
// });
