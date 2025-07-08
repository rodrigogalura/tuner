<?php

use Laradigs\Tweaker\Projection;
use Illuminate\Database\Eloquent\Model;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;

beforeEach(function() {
    Mockery::globalHelpers();

    $this->model = mock(Model::class);
    $this->COLUMN_LISTING = ['id', 'name'];
});

describe('Exception Logic', function () {
    it('should throw an exception if one of projectable fields is invalid', function () {
        // Prepare
        $this->model->shouldReceive('columnListing')
            ->andReturn($this->COLUMN_LISTING);
        $invalidProjectableFields = ['not_exists_field'];

        $projection = new Projection($this->model, []);

        // Act & Assert
        expect(fn () => $projection->setSelectFields($invalidProjectableFields))->toThrow(InvalidFieldsException::class);
    })->done();

    it('should throw an exception if one of defined fields is invalid', function () {
        // Prepare
        $definedFields = new \stdClass;
        $definedFields->columns = ['not_exists_field'];

        $projectableFields = $this->COLUMN_LISTING;
        $this->model
            ->shouldReceive('columnListing')
            ->andReturn($this->COLUMN_LISTING)
            ->shouldReceive('getQuery')
            ->andReturn($definedFields);

        $projection = new Projection($this->model, []);

        // Act & Assert
        expect(fn () => $projection->setSelectFields($projectableFields))->toThrow(InvalidFieldsException::class);
    })->done();
});

it('should not perform any action if the projectable field\'s value is empty', function () {
    // Prepare
    $projection = new Projection($this->model, []);

    // Act & Assert
    expect($projection->setSelectFields([]))->toBeNull();
})
->done();

test('All valid scenarios', function (array $projectable, array $definedFields, array $clientInput, array $resultFields) {
    // Prepare
    $definedFieldsObj = new \stdClass;
    $definedFieldsObj->columns = $definedFields;

    $this->model
        ->shouldReceive('columnListing')
        ->andReturn($this->COLUMN_LISTING)
        ->shouldReceive('getQuery')
        ->andReturn($definedFieldsObj)
        ->shouldReceive('getHidden')
        ->andReturn([]);

    $projection = new Projection($this->model, $clientInput);

    // Act
    $projection->setSelectFields($projectable);

    // Act & Assert
    expect($projection->getSelectFields())->toBe($resultFields);
})->with([
    [
        'projectable' => ['*'],    'definedFields' => ['*'],            'clientInput' => ['fields' => '*'],            'resultFields' => ['id', 'name'],
    ],
    [
        'projectable' => ['*'],    'definedFields' => ['*'],            'clientInput' => ['fields' => 'id'],           'resultFields' => ['id'],
    ],
    [
        'projectable' => ['*'],    'definedFields' => ['*'],            'clientInput' => ['fields' => 'id, name'],     'resultFields' => ['id', 'name'],
    ],

    [
        'projectable' => ['*'],    'definedFields' => ['id'],           'clientInput' => ['fields' => '*'],            'resultFields' => ['id'],
    ],
    [
        'projectable' => ['*'],    'definedFields' => ['id'],           'clientInput' => ['fields' => 'id'],           'resultFields' => ['id'],
    ],
    [
        'projectable' => ['*'],    'definedFields' => ['id'],           'clientInput' => ['fields' => 'id, name'],     'resultFields' => ['id'],
    ],

    [
        'projectable' => ['*'],    'definedFields' => ['id', 'name'],     'clientInput' => ['fields' => '*'],            'resultFields' => ['id', 'name'],
    ],
    [
        'projectable' => ['*'],    'definedFields' => ['id', 'name'],     'clientInput' => ['fields' => 'id'],           'resultFields' => ['id'],
    ],
    [
        'projectable' => ['*'],    'definedFields' => ['id', 'name'],     'clientInput' => ['fields' => 'id, name'],     'resultFields' => ['id', 'name'],
    ],

    //

    [
        'projectable' => ['id'],   'definedFields' => ['*'],            'clientInput' => ['fields' => '*'],            'resultFields' => ['id'],
    ],
    [
        'projectable' => ['id'],   'definedFields' => ['*'],            'clientInput' => ['fields' => 'id'],           'resultFields' => ['id'],
    ],
    [
        'projectable' => ['id'],   'definedFields' => ['*'],            'clientInput' => ['fields' => 'id, name'],     'resultFields' => ['id'],
    ],

    [
        'projectable' => ['id'],   'definedFields' => ['id'],           'clientInput' => ['fields' => '*'],            'resultFields' => ['id'],
    ],
    [
        'projectable' => ['id'],   'definedFields' => ['id'],           'clientInput' => ['fields' => 'id'],           'resultFields' => ['id'],
    ],
    [
        'projectable' => ['id'],   'definedFields' => ['id'],           'clientInput' => ['fields' => 'id, name'],     'resultFields' => ['id'],
    ],

    [
        'projectable' => ['id'],   'definedFields' => ['id', 'name'],     'clientInput' => ['fields' => '*'],            'resultFields' => ['id'],
    ],
    [
        'projectable' => ['id'],   'definedFields' => ['id', 'name'],     'clientInput' => ['fields' => 'id'],           'resultFields' => ['id'],
    ],
    [
        'projectable' => ['id'],   'definedFields' => ['id', 'name'],     'clientInput' => ['fields' => 'id, name'],     'resultFields' => ['id'],
    ],

    //

    [
        'projectable' => ['id', 'name'],    'definedFields' => ['*'],              'clientInput' => ['fields' => '*'],            'resultFields' => ['id', 'name'],
    ],
    [
        'projectable' => ['id', 'name'],    'definedFields' => ['*'],              'clientInput' => ['fields' => 'id'],           'resultFields' => ['id'],
    ],
    [
        'projectable' => ['id', 'name'],    'definedFields' => ['*'],              'clientInput' => ['fields' => 'id, name'],     'resultFields' => ['id', 'name'],
    ],

    [
        'projectable' => ['id', 'name'],    'definedFields' => ['id'],             'clientInput' => ['fields' => '*'],            'resultFields' => ['id'],
    ],
    [
        'projectable' => ['id', 'name'],    'definedFields' => ['id'],             'clientInput' => ['fields' => 'id'],           'resultFields' => ['id'],
    ],
    [
        'projectable' => ['id', 'name'],    'definedFields' => ['id'],             'clientInput' => ['fields' => 'id, name'],     'resultFields' => ['id'],
    ],

    [
        'projectable' => ['id', 'name'],    'definedFields' => ['id', 'name'],     'clientInput' => ['fields' => '*'],            'resultFields' => ['id', 'name'],
    ],
    [
        'projectable' => ['id', 'name'],    'definedFields' => ['id', 'name'],     'clientInput' => ['fields' => 'id'],           'resultFields' => ['id'],
    ],
    [
        'projectable' => ['id', 'name'],    'definedFields' => ['id', 'name'],     'clientInput' => ['fields' => 'id, name'],     'resultFields' => ['id', 'name'],
    ]
])->done();
