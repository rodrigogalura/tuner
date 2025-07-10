<?php

use Laradigs\Tweaker\Projection;
use Illuminate\Database\Eloquent\Model;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use RGalura\ApiIgniter\Exceptions\NoDefinedFieldException;

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

describe('Not perform any action', function () {
    it('should not perform any action if neither "fields" nor "fields!" is used', function () {
        // Prepare
        $projection = new Projection(
            $this->model,
            projectableFields: $this->visibleFields,
            definedFields: ['*'],
            clientInput: [],
        );

        // Act & Assert
        expect($projection->handle())->toBeNull();
    });

    it('should not perform any action if both "fields" and "fields!" are used', function () {
        // Prepare
        $projection = new Projection(
            $this->model,
            projectableFields: $this->visibleFields,
            definedFields: ['*'],
            clientInput: ['fields' => '*', 'fields!' => '*'],
        );

        // Act & Assert
        expect($projection->handle())->toBeNull();
    });

    it('should not perform any action if the client input "fields!" is "*"', function () {
        // Prepare
        $projection = new Projection(
            $this->model,
            projectableFields: $this->visibleFields,
            definedFields: ['*'],
            clientInput: ['fields!' => '*'],
        );

        // Act & Assert
        expect($projection->handle())->toBeNull();
    });

    it('should not perform any action if the projectable field\'s value is empty', function () {
        // Prepare
        $projection = new Projection(
            $this->model,
            projectableFields: [],
            definedFields: ['*'],
            clientInput: ['fields' => implode(',', $this->visibleFields)],
        );

        // Act & Assert
        expect($projection->handle())->toBeNull();
    });

    it('should not perform any action if the projectable fields and defined fields are not intersect', function () {
        // Prepare
        $projection = new Projection(
            $this->model,
            projectableFields: [$this->visibleFields[0]],
            definedFields: [$this->visibleFields[1]],
            clientInput: [],
        );

        // Act & Assert
        expect($projection->handle())->toBeNull();
    });
});

describe('Throw an exception', function () {
    it('should throw an exception if one of projectable fields is invalid', function () {
        // Prepare
        $notInVisibleFields = ['email'];
        $projection = new Projection(
            $this->model,
            projectableFields: $notInVisibleFields,
            definedFields: [],
            clientInput: ['fields' => implode(',', $this->visibleFields)],
        );

        // Act & Assert
        expect(fn () => $projection->handle())->toThrow(InvalidFieldsException::class);
    });

    it('should throw an exception if the defined fields is empty', function () {
        // Prepare
        $projection = new Projection(
            $this->model,
            projectableFields: ['id'],
            definedFields: [],
            clientInput: ['fields' => implode(',', $this->visibleFields)],
        );

        // Act & Assert
        expect(fn () => $projection->handle())->toThrow(NoDefinedFieldException::class);
    });

    it('should throw an exception if one of defined fields is invalid', function () {
        // Prepare
        $notInVisibleFields = ['email'];
        $projection = new Projection(
            $this->model,
            projectableFields: ['id'],
            definedFields: $notInVisibleFields,
            clientInput: ['fields' => implode(',', $this->visibleFields)],
        );

        // Act & Assert
        expect(fn () => $projection->handle())->toThrow(InvalidFieldsException::class);
    });
});

describe('Valid scenarios', function () {
    it('should passed all valid scenarios', function ($projectableFields, $definedFields, $clientInput, $expectedResult) {
        // Prepare
        $projection = new Projection(
            $this->model,
            $projectableFields,
            $definedFields,
            ['fields' => $clientInput],
        );

        $projection2 = new Projection(
            $this->model,
            $projectableFields,
            $definedFields,
            ['fields!' => $clientInput],
        );

        // Act & Assert
        expect($projection->handle()?->getProjectedFields())->toBe($expectedResult['fields']);
        expect($projection2->handle()?->getProjectedFields())->toBe($expectedResult['fields!']);
    })
    ->with('truth-table');
});
