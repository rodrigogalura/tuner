<?php

use Workbench\App\Models\AllFieldsAreProjectableModel;
use Workbench\App\Models\NoProjectableModel;
use Workbench\App\Models\OnlyIdAndNameAreProjectableModel;
use Workbench\App\Models\OnlyIdIsProjectableModel;
use Workbench\App\Models\OnlyNameIsProjectableModel;

use function Pest\Laravel\get;

define('INTERSECT_KEY', 'fields');
define('EXCEPT_KEY', 'fields!');

dataset('not-string-value', [
    [[1]], [['1']],
    [[10]], [['20']],
    [[100]], [['300']],
    [['a']], [['A']],
    [['@']], [['!']],
]);

beforeEach(function (): void {
    $_GET = [];

    $this->validFields = ['id', 'name'];
    $this->validFieldsString = 'id, name';
});

describe('Prerequisites', function (): void {
    it('should not perform projection', function ($key): void {
        // Prepare
        $_GET['defined_fields'] = $this->validFields;
        $_GET[$key] = 'id';
        NoProjectableModel::factory()->create();

        // Act & Assert
        get('/api/no-projectable')
            ->assertOk()
            ->assertExactJsonStructure(['*' => $this->validFields]);
    })
        ->with([INTERSECT_KEY, EXCEPT_KEY]);

    it('should throw InvalidProjectableException if all projectable fields are invalid', function ($key): void {
        $_GET[$key] = 'id';

        // Act & Assert
        get('/api/invalid-projectable')
            ->assertServerError();
    })
        ->with([INTERSECT_KEY, EXCEPT_KEY]);

    it('should throw DefinedFieldsAreEmptyException if the defined fields are empty', function ($key): void {
        $_GET[$key] = $this->validFieldsString;
        $_GET['defined_fields'] = [];

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertServerError();
    })
        ->with([INTERSECT_KEY, EXCEPT_KEY]);

    it('should throw InvalidDefinedFieldsException if one of defined fields is not in valid fields', function ($key): void {
        $_GET[$key] = $this->validFieldsString;
        $_GET['defined_fields'] = ['email'];

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertServerError();
    })
        ->with([INTERSECT_KEY, EXCEPT_KEY]);

    it('should throw InvalidDefinedFieldsException if one of defined fields is not in projectable fields', function ($key): void {
        $_GET[$key] = $this->validFieldsString;
        $_GET['defined_fields'] = ['name'];

        // Act & Assert
        get('/api/only-id-is-projectable')
            ->assertServerError();
    })
        ->with([INTERSECT_KEY, EXCEPT_KEY]);
});

describe('Validations', function (): void {
    it('should throw ValidationException if both intersect and except are used at the same time', function (): void {
        $_GET[INTERSECT_KEY] = $this->validFieldsString;
        $_GET[EXCEPT_KEY] = $this->validFieldsString;

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertUnprocessable();
    });

    it('should throw ValidationException if the input type is not string', function ($key, $value): void {
        $_GET[$key] = $value;

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertUnprocessable();
    })
        ->with([INTERSECT_KEY, EXCEPT_KEY])
        ->with('not-string-value');

    it('should throw ValidationException if the except input is asterisk(*)', function (): void {
        $_GET[EXCEPT_KEY] = '*';

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertUnprocessable();
    });
});

// describe('Not perform any action.', function (): void {
//     it('should not perform any action if the projectable fields and defined fields are not intersect', function (): void {
//         // Prepare
//         $_GET['defined_fields'] = ['name'];
//         $data = OnlyIdIsProjectableModel::factory()->create();

//         // Act & Assert
//         get('/api/only-id-is-projectable')
//             ->assertOk()
//             ->assertJsonCount($data->count())
//             ->assertExactJsonStructure(['*' => $_GET['defined_fields']]);
//     });
// });

// describe('Throw an exception', function (): void {

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

describe('Valid scenarios', function (): void {
    // it('should passed all valid scenarios for intersect projection', function ($projectableFields, $definedFields, $clientInput, $intersectResult): void {
    //     // Prepare
    //     $equivalentRoutes = [
    //         '*' => '/api/all-fields-are-projectable',
    //         'id' => '/api/only-id-is-projectable',
    //         'name' => '/api/only-name-is-projectable',
    //         'id, name' => '/api/only-id-and-name-are-projectable',
    //         'empty' => '/api/no-projectable',
    //     ];

    //     $models = [
    //         '*' => AllFieldsAreProjectableModel::class,
    //         'id' => OnlyIdIsProjectableModel::class,
    //         'name' => OnlyNameIsProjectableModel::class,
    //         'id, name' => OnlyIdAndNameAreProjectableModel::class,
    //         'empty' => NoProjectableModel::class,
    //     ];

    //     $key = implode(', ', $projectableFields);

    //     $_GET['defined_fields'] = $definedFields;

    //     $model = $models[$key];
    //     $data = $model::factory(rand(2, 5))->create();

    //     $route = $equivalentRoutes[$key];

    //     $_GET['fields'] = $clientInput;

    //     // Act & Assert
    //     get($route)
    //         ->assertOk()
    //         ->assertJsonCount(empty($intersectResult) ? 0 : $data->count())
    //         ->assertExactJsonStructure(['*' => $intersectResult]);
    // })
    //     ->with('intersect-projection-truth-table');

    it('should passed all valid scenarios', function ($projectableFields, $definedFields, $clientInput, $exceptResult): void {
        // Prepare
        $equivalentRoutes = [
            '*' => '/api/all-fields-are-projectable',
            'id' => '/api/only-id-is-projectable',
            'name' => '/api/only-name-is-projectable',
            'id, name' => '/api/only-id-and-name-are-projectable',
            'empty' => '/api/no-projectable',
        ];

        $models = [
            '*' => AllFieldsAreProjectableModel::class,
            'id' => OnlyIdIsProjectableModel::class,
            'name' => OnlyNameIsProjectableModel::class,
            'id, name' => OnlyIdAndNameAreProjectableModel::class,
            'empty' => NoProjectableModel::class,
        ];

        $key = implode(', ', $projectableFields);

        $_GET['defined_fields'] = $definedFields;

        $model = $models[$key];
        $data = $model::factory(rand(2, 5))->create();

        $route = $equivalentRoutes[$key];

        // $_GET['fields'] = $clientInput;

        // Act & Assert
        // get($route)
        //     ->assertOk()
        //     ->assertJsonCount(empty($intersectResult) ? 0 : $data->count())
        //     ->assertExactJsonStructure(['*' => $intersectResult]);

        // unset($_GET['fields']);
        $_GET[EXCEPT_KEY] = $clientInput;

        // Act & Assert
        get($route)
            ->assertOk()
            // ->assertJsonCount(empty($exceptResult) ? 0 : $data->count())
            ->assertExactJsonStructure(['*' => $exceptResult]);
    })
        ->with('except-projection-truth-table')->only();
});
