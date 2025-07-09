<?php

use Illuminate\Database\Eloquent\Model;
use function RGalura\ApiIgniter\filter_explode;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;

class Projection
{
    private readonly array $projectedFields;

    private string $clientInputFieldsKey = 'fields';
    private string $clientInputFieldsNotKey = 'fields!';

    public function __construct(
        private Model $model,
        private array $projectableFields,
        private array $definedFields,
        private array $clientInputFields,
    )
    {
        //
    }

    public function setClientInputFieldsKey($key)
    {
        $this->clientInputFieldsKey = $key;
    }

    public function setClientInputFieldsNotKey($key)
    {
        $this->clientInputFieldsNotKey = $key;
    }

    private function convertToValuesIfAsterisk(&$var)
    {
        if ($var === ['*']) {
            $var = $this->visibleFields();
        }
    }

    public function visibleFields()
    {
        return $this->model->getConnection()->getSchemaBuilder()->getColumnListing($this->model->getTable());
    }

    private function throwIfInvalidFields(array $fields)
    {
        if (! empty($diff = array_diff($fields, $this->visibleFields()))) {
            throw new InvalidFieldsException($diff, 1);
        }
    }

    public function handle()
    {
        if (empty($this->projectableFields)) {
            return;
        }

        $include = $this->clientInputFields[$this->clientInputFieldsKey] ?? null;
        $exclude = $this->clientInputFields[$this->clientInputFieldsNotKey] ?? null;

        if (isset($include, $exclude)) {
            return;
        }

        $this->convertToValuesIfAsterisk($this->projectableFields);
        $this->throwIfInvalidFields($this->projectableFields);

        $this->convertToValuesIfAsterisk($this->definedFields);
        $this->throwIfInvalidFields($this->definedFields);

        $this->projectableFields = array_intersect($this->projectableFields, $this->definedFields);

        $includeFn = function (array $include) {
            return match (true) {
                $include === ['*'] => $this->projectableFields,
                default => array_intersect($this->projectableFields, $include)
            };
        };

        // $excludeFn = function (array $projectable, array $exclude) {
        //     return match (true) {
        //         $exclude === ['*'] => throw new ExcludeFieldsException($exclude),
        //         ! empty($diff = array_diff($exclude, $projectable)) => throw new InvalidFieldsException(array_values($diff)),
        //         default => array_diff($projectable, $exclude)
        //     };
        // };

        $this->projectedFields = match (true) {
            isset($include) => $includeFn(filter_explode($include)),
            // isset($exclude) => $excludeFn($projectable, filter_explode($exclude)),
            default => null
        };

        return $this;
    }

    public function getProjectedFields()
    {
        return $this->projectedFields;
    }
}

beforeEach(function() {
    Mockery::globalHelpers();

    $table = 'users';
    $this->visibleFields = ['id', 'name'];

    $this->model = mock(Model::class);
    $this->model
        ->shouldReceive('getTable')
        ->andReturn($table)
        ->shouldReceive('getConnection->getSchemaBuilder->getColumnListing')
        ->with(Mockery::type('string'))
        ->andReturn($this->visibleFields)
        ;
});

afterEach(function() {
    Mockery::close();
});

it('should throw an exception if one of projectable fields is invalid', function () {
    // Prepare
    $notInVisibleFields = ['email'];
    $projection = new Projection(
        $this->model,
        projectableFields: $notInVisibleFields,
        definedFields: [],
        clientInputFields: [],
    );

    // Act & Assert
    expect(fn () => $projection->handle())->toThrow(InvalidFieldsException::class);
});

it('should throw an exception if one of defined fields is invalid', function () {
    // Prepare
    $notInVisibleFields = ['email'];
    $projection = new Projection(
        $this->model,
        projectableFields: ['id'],
        definedFields: $notInVisibleFields,
        clientInputFields: [],
    );

    // Act & Assert
    expect(fn () => $projection->handle())->toThrow(InvalidFieldsException::class);
});

it('should not perform any action if the projectable field\'s value is empty', function () {
    // Prepare
    $projection = new Projection(
        $this->model,
        projectableFields: [],
        definedFields: [],
        clientInputFields: [],
    );

    // Act & Assert
    expect($projection->handle())->toBeNull();
});

it('should not perform any action if the fields and fields! used at the same time', function () {
    // Prepare
    $projection = new Projection(
        $this->model,
        projectableFields: $this->visibleFields,
        definedFields: ['*'],
        clientInputFields: ['fields' => '*', 'fields!' => '*'],
    );

    // Act & Assert
    expect($projection->handle())->toBeNull();
});

it('should passed all valid scenarios', function ($projectableFields, $definedFields, $clientInputFields, $resultFields) {
    // Prepare
    $projection = new Projection(
        $this->model,
        $projectableFields,
        $definedFields,
        $clientInputFields,
    );

    // Act & Assert
    expect($projection->handle()->getProjectedFields())->toBe($resultFields);
})->with([
    [
        'projectableFields' => ['*'],    'definedFields' => ['*'],              'clientInputFields' => ['fields' => '*'],            'resultFields' => ['id', 'name'],
    ],
    [
        'projectableFields' => ['*'],    'definedFields' => ['*'],              'clientInputFields' => ['fields' => 'id'],           'resultFields' => ['id'],
    ],
    [
        'projectableFields' => ['*'],    'definedFields' => ['*'],              'clientInputFields' => ['fields' => 'id, name'],     'resultFields' => ['id', 'name'],
    ],

    [
        'projectableFields' => ['*'],    'definedFields' => ['id'],             'clientInputFields' => ['fields' => '*'],            'resultFields' => ['id'],
    ],
    [
        'projectableFields' => ['*'],    'definedFields' => ['id'],             'clientInputFields' => ['fields' => 'id'],           'resultFields' => ['id'],
    ],
    [
        'projectableFields' => ['*'],    'definedFields' => ['id'],             'clientInputFields' => ['fields' => 'id, name'],     'resultFields' => ['id'],
    ],

    [
        'projectableFields' => ['*'],    'definedFields' => ['id', 'name'],     'clientInputFields' => ['fields' => '*'],            'resultFields' => ['id', 'name'],
    ],
    [
        'projectableFields' => ['*'],    'definedFields' => ['id', 'name'],     'clientInputFields' => ['fields' => 'id'],           'resultFields' => ['id'],
    ],
    [
        'projectableFields' => ['*'],    'definedFields' => ['id', 'name'],     'clientInputFields' => ['fields' => 'id, name'],     'resultFields' => ['id', 'name'],
    ],

    //

    [
        'projectableFields' => ['id'],   'definedFields' => ['*'],            'clientInputFields' => ['fields' => '*'],              'resultFields' => ['id'],
    ],
    [
        'projectableFields' => ['id'],   'definedFields' => ['*'],            'clientInputFields' => ['fields' => 'id'],             'resultFields' => ['id'],
    ],
    [
        'projectableFields' => ['id'],   'definedFields' => ['*'],            'clientInputFields' => ['fields' => 'id, name'],       'resultFields' => ['id'],
    ],

    [
        'projectableFields' => ['id'],   'definedFields' => ['id'],           'clientInputFields' => ['fields' => '*'],              'resultFields' => ['id'],
    ],
    [
        'projectableFields' => ['id'],   'definedFields' => ['id'],           'clientInputFields' => ['fields' => 'id'],             'resultFields' => ['id'],
    ],
    [
        'projectableFields' => ['id'],   'definedFields' => ['id'],           'clientInputFields' => ['fields' => 'id, name'],       'resultFields' => ['id'],
    ],

    [
        'projectableFields' => ['id'],   'definedFields' => ['id', 'name'],     'clientInputFields' => ['fields' => '*'],            'resultFields' => ['id'],
    ],
    [
        'projectableFields' => ['id'],   'definedFields' => ['id', 'name'],     'clientInputFields' => ['fields' => 'id'],           'resultFields' => ['id'],
    ],
    [
        'projectableFields' => ['id'],   'definedFields' => ['id', 'name'],     'clientInputFields' => ['fields' => 'id, name'],     'resultFields' => ['id'],
    ],

    //

    [
        'projectableFields' => ['id', 'name'],    'definedFields' => ['*'],              'clientInputFields' => ['fields' => '*'],            'resultFields' => ['id', 'name'],
    ],
    [
        'projectableFields' => ['id', 'name'],    'definedFields' => ['*'],              'clientInputFields' => ['fields' => 'id'],           'resultFields' => ['id'],
    ],
    [
        'projectableFields' => ['id', 'name'],    'definedFields' => ['*'],              'clientInputFields' => ['fields' => 'id, name'],     'resultFields' => ['id', 'name'],
    ],

    [
        'projectableFields' => ['id', 'name'],    'definedFields' => ['id'],             'clientInputFields' => ['fields' => '*'],            'resultFields' => ['id'],
    ],
    [
        'projectableFields' => ['id', 'name'],    'definedFields' => ['id'],             'clientInputFields' => ['fields' => 'id'],           'resultFields' => ['id'],
    ],
    [
        'projectableFields' => ['id', 'name'],    'definedFields' => ['id'],             'clientInputFields' => ['fields' => 'id, name'],     'resultFields' => ['id'],
    ],

    [
        'projectableFields' => ['id', 'name'],    'definedFields' => ['id', 'name'],     'clientInputFields' => ['fields' => '*'],            'resultFields' => ['id', 'name'],
    ],
    [
        'projectableFields' => ['id', 'name'],    'definedFields' => ['id', 'name'],     'clientInputFields' => ['fields' => 'id'],           'resultFields' => ['id'],
    ],
    [
        'projectableFields' => ['id', 'name'],    'definedFields' => ['id', 'name'],     'clientInputFields' => ['fields' => 'id, name'],     'resultFields' => ['id', 'name'],
    ]
]);

























// use Laradigs\Tweaker\Projection;
// use Illuminate\Database\Eloquent\Model;
// use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;

// beforeEach(function() {
//     Mockery::globalHelpers();

//     $this->model = mock(Model::class);
//     $this->COLUMN_LISTING = ['id', 'name'];

//     // ->model->getConnection()->getSchemaBuilder()->getColumnListing($this->model->getTable());
//     // ->model->getHidden()
//     // ->model->getQuery()->columns ?? null)) {
// });

// describe('Exception Logic', function () {
//     it('should throw an exception if one of projectable fields is invalid', function () {
//         // Prepare
//         $this->model
//             ->shouldReceive('getTable')
//             ->andReturn('users')
//             ->shouldReceive('getConnection->getSchemaBuilder->getColumnListing')
//             ->with(Mockery::type('string'))
//             ->andReturn($this->COLUMN_LISTING);
//         $invalidProjectableFields = ['not_exists_field'];

//         $projection = new Projection(
//             $this->model,
//             projectableFields: $invalidProjectableFields,
//             definedFields: [],
//             clientInputFields: [],
//         );

//         // Act & Assert
//         expect(fn () => $projection->setSelectFields())->toThrow(InvalidFieldsException::class);
//     })->only();

//     it('should throw an exception if one of defined fields is invalid', function () {
//         // Prepare
//         $definedFields = new \stdClass;
//         $definedFields->columns = ['not_exists_field'];

//         $projectableFields = $this->COLUMN_LISTING;
//         $this->model
//             ->shouldReceive('columnListing')
//             ->andReturn($this->COLUMN_LISTING)
//             ->shouldReceive('getQuery')
//             ->andReturn($definedFields);

//         $projection = new Projection($this->model, []);

//         // Act & Assert
//         expect(fn () => $projection->setSelectFields($projectableFields))->toThrow(InvalidFieldsException::class);
//     });
// });

// it('should not perform any action if the projectable field\'s value is empty', function () {
//     // Prepare
//     $projection = new Projection($this->model, []);

//     // Act & Assert
//     expect($projection->setSelectFields([]))->toBeNull();
// })
// ;

// test('All valid scenarios', function (array $projectable, array $definedFields, array $clientInputFields, array $resultFields) {
//     // Prepare
//     $definedFieldsObj = new \stdClass;
//     $definedFieldsObj->columns = $definedFields;

//     $this->model
//         ->shouldReceive('columnListing')
//         ->andReturn($this->COLUMN_LISTING)
//         ->shouldReceive('getQuery')
//         ->andReturn($definedFieldsObj)
//         ->shouldReceive('getHidden')
//         ->andReturn([]);

//     $projection = new Projection($this->model, $clientInputFields);

//     // Act
//     $projection->setSelectFields($projectable);

//     // Act & Assert
//     expect($projection->getSelectFields())->toBe($resultFields);
// })->with([
//     [
//         'projectableFields' => ['*'],    'definedFields' => ['*'],            'clientInputFields' => ['fields' => '*'],            'resultFields' => ['id', 'name'],
//     ],
//     // [
//     //     'projectableFields' => ['*'],    'definedFields' => ['*'],            'clientInputFields' => ['fields' => 'id'],           'resultFields' => ['id'],
//     // ],
//     // [
//     //     'projectableFields' => ['*'],    'definedFields' => ['*'],            'clientInputFields' => ['fields' => 'id, name'],     'resultFields' => ['id', 'name'],
//     // ],

//     // [
//     //     'projectableFields' => ['*'],    'definedFields' => ['id'],           'clientInputFields' => ['fields' => '*'],            'resultFields' => ['id'],
//     // ],
//     // [
//     //     'projectableFields' => ['*'],    'definedFields' => ['id'],           'clientInputFields' => ['fields' => 'id'],           'resultFields' => ['id'],
//     // ],
//     // [
//     //     'projectableFields' => ['*'],    'definedFields' => ['id'],           'clientInputFields' => ['fields' => 'id, name'],     'resultFields' => ['id'],
//     // ],

//     // [
//     //     'projectableFields' => ['*'],    'definedFields' => ['id', 'name'],     'clientInputFields' => ['fields' => '*'],            'resultFields' => ['id', 'name'],
//     // ],
//     // [
//     //     'projectableFields' => ['*'],    'definedFields' => ['id', 'name'],     'clientInputFields' => ['fields' => 'id'],           'resultFields' => ['id'],
//     // ],
//     // [
//     //     'projectableFields' => ['*'],    'definedFields' => ['id', 'name'],     'clientInputFields' => ['fields' => 'id, name'],     'resultFields' => ['id', 'name'],
//     // ],

//     // //

//     // [
//     //     'projectableFields' => ['id'],   'definedFields' => ['*'],            'clientInputFields' => ['fields' => '*'],            'resultFields' => ['id'],
//     // ],
//     // [
//     //     'projectableFields' => ['id'],   'definedFields' => ['*'],            'clientInputFields' => ['fields' => 'id'],           'resultFields' => ['id'],
//     // ],
//     // [
//     //     'projectableFields' => ['id'],   'definedFields' => ['*'],            'clientInputFields' => ['fields' => 'id, name'],     'resultFields' => ['id'],
//     // ],

//     // [
//     //     'projectableFields' => ['id'],   'definedFields' => ['id'],           'clientInputFields' => ['fields' => '*'],            'resultFields' => ['id'],
//     // ],
//     // [
//     //     'projectableFields' => ['id'],   'definedFields' => ['id'],           'clientInputFields' => ['fields' => 'id'],           'resultFields' => ['id'],
//     // ],
//     // [
//     //     'projectableFields' => ['id'],   'definedFields' => ['id'],           'clientInputFields' => ['fields' => 'id, name'],     'resultFields' => ['id'],
//     // ],

//     // [
//     //     'projectableFields' => ['id'],   'definedFields' => ['id', 'name'],     'clientInputFields' => ['fields' => '*'],            'resultFields' => ['id'],
//     // ],
//     // [
//     //     'projectableFields' => ['id'],   'definedFields' => ['id', 'name'],     'clientInputFields' => ['fields' => 'id'],           'resultFields' => ['id'],
//     // ],
//     // [
//     //     'projectableFields' => ['id'],   'definedFields' => ['id', 'name'],     'clientInputFields' => ['fields' => 'id, name'],     'resultFields' => ['id'],
//     // ],

//     // //

//     // [
//     //     'projectableFields' => ['id', 'name'],    'definedFields' => ['*'],              'clientInputFields' => ['fields' => '*'],            'resultFields' => ['id', 'name'],
//     // ],
//     // [
//     //     'projectableFields' => ['id', 'name'],    'definedFields' => ['*'],              'clientInputFields' => ['fields' => 'id'],           'resultFields' => ['id'],
//     // ],
//     // [
//     //     'projectableFields' => ['id', 'name'],    'definedFields' => ['*'],              'clientInputFields' => ['fields' => 'id, name'],     'resultFields' => ['id', 'name'],
//     // ],

//     // [
//     //     'projectableFields' => ['id', 'name'],    'definedFields' => ['id'],             'clientInputFields' => ['fields' => '*'],            'resultFields' => ['id'],
//     // ],
//     // [
//     //     'projectableFields' => ['id', 'name'],    'definedFields' => ['id'],             'clientInputFields' => ['fields' => 'id'],           'resultFields' => ['id'],
//     // ],
//     // [
//     //     'projectableFields' => ['id', 'name'],    'definedFields' => ['id'],             'clientInputFields' => ['fields' => 'id, name'],     'resultFields' => ['id'],
//     // ],

//     // [
//     //     'projectableFields' => ['id', 'name'],    'definedFields' => ['id', 'name'],     'clientInputFields' => ['fields' => '*'],            'resultFields' => ['id', 'name'],
//     // ],
//     // [
//     //     'projectableFields' => ['id', 'name'],    'definedFields' => ['id', 'name'],     'clientInputFields' => ['fields' => 'id'],           'resultFields' => ['id'],
//     // ],
//     // [
//     //     'projectableFields' => ['id', 'name'],    'definedFields' => ['id', 'name'],     'clientInputFields' => ['fields' => 'id, name'],     'resultFields' => ['id', 'name'],
//     // ]
// ]);
