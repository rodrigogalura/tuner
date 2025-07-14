<?php

use Illuminate\Database\Eloquent\Model;
use Laradigs\Tweaker\Projection\NoActionWillPerformException;
use Laradigs\Tweaker\Projection\ProjectionField;
use Laradigs\Tweaker\Projection\ProjectionFieldNot;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use RGalura\ApiIgniter\Exceptions\NoDefinedFieldException;

use function RGalura\ApiIgniter\filter_explode;

beforeEach(function () {
    Mockery::globalHelpers();

    $table = 'users';
    $this->visibleFields = ['id', 'name'];

    $this->model = mock(Model::class);
    $this->model
        ->shouldReceive('getTable')
        ->andReturn($table)
        ->shouldReceive('getHidden')
        ->andReturn([])
        ->shouldReceive('getConnection->getSchemaBuilder->getColumnListing')
        ->with(Mockery::type('string'))
        ->andReturn($this->visibleFields);
});

afterEach(function () {
    Mockery::close();
});

describe('Not perform any action. Just return defined value as default.', function () {
    it('should not perform any action if the client input "fields!" is "*"', function () {
        // Prepare
        $DEFINE_FIELDS = ['*'];
        $projection = new ProjectionFieldNot(
            $this->model,
            projectableFields: $this->visibleFields,
            definedFields: $DEFINE_FIELDS,
            clientInput: ['*'],
        );

        // Act & Assert
        expect(fn () => $projection->project())->toThrow(NoActionWillPerformException::class);
    });

    it('should not perform any action if the projectable field\'s value is empty', function () {
        // Prepare
        $DEFINE_FIELDS = ['*'];
        $projection = new ProjectionField(
            $this->model,
            projectableFields: [],
            definedFields: $DEFINE_FIELDS,
            clientInput: [],
        );

        // Act & Assert
        expect(fn () => $projection->project())->toThrow(NoActionWillPerformException::class);
    });

    it('should not perform any action if the projectable fields and defined fields are not intersect', function () {
        // Prepare
        $DEFINE_FIELDS = [$this->visibleFields[1]];
        $projection = new ProjectionField(
            $this->model,
            projectableFields: [$this->visibleFields[0]],
            definedFields: $DEFINE_FIELDS,
            clientInput: [],
        );

        // Act & Assert
        expect(fn () => $projection->project())->toThrow(NoActionWillPerformException::class);
    });
});

describe('Throw an exception', function () {
    it('should throw an exception if one of projectable fields is invalid', function () {
        // Prepare
        $notInVisibleFields = ['email'];
        $projection = new ProjectionField(
            $this->model,
            projectableFields: $notInVisibleFields,
            definedFields: [],
            clientInput: $this->visibleFields,
        );

        // Act & Assert
        expect(fn () => $projection->project())->toThrow(InvalidFieldsException::class);
    });

    it('should throw an exception if the defined fields is empty', function () {
        // Prepare
        $projection = new ProjectionField(
            $this->model,
            projectableFields: ['id'],
            definedFields: [],
            clientInput: $this->visibleFields,
        );

        // Act & Assert
        expect(fn () => $projection->project())->toThrow(NoDefinedFieldException::class);
    });

    it('should throw an exception if one of defined fields is invalid', function () {
        // Prepare
        $notInVisibleFields = ['email'];
        $projection = new ProjectionField(
            $this->model,
            projectableFields: ['id'],
            definedFields: $notInVisibleFields,
            clientInput: $this->visibleFields,
        );

        // Act & Assert
        expect(fn () => $projection->project())->toThrow(InvalidFieldsException::class);
    });
});

describe('Valid scenarios', function () {
    it('should passed all valid scenarios for client input "fields"', function ($projectableFields, $definedFields, $clientInput, $expectedResult) {
        // Prepare
        $projection = new ProjectionField(
            $this->model,
            $projectableFields,
            $definedFields,
            filter_explode($clientInput)
        );

        // Act & Assert
        expect($projection->project())->toBe($expectedResult);
    })
        ->with('fields-truth-table');

    it('should passed all valid scenarios for client input "fields!"', function ($projectableFields, $definedFields, $clientInput, $expectedResult) {
        // Prepare
        $projection = new ProjectionFieldNot(
            $this->model,
            $projectableFields,
            $definedFields,
            filter_explode($clientInput)
        );

        // Act & Assert
        expect($projection->project())->toBe($expectedResult);
    })
        ->with('fields-not-truth-table');
});
