<?php

use Illuminate\Database\Eloquent\Model;
use Laradigs\Tweaker\Projection\NoActionWillPerformException;
use Laradigs\Tweaker\Projection\ProjectionField;
use Laradigs\Tweaker\Projection\ProjectionFieldNot;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use RGalura\ApiIgniter\Exceptions\NoDefinedFieldException;

use function RGalura\ApiIgniter\filter_explode;

beforeEach(function (): void {
    Mockery::globalHelpers();

    $table = 'users';
    $this->visibleFields = ['id', 'name'];
    $this->visibleFieldsString = implode(', ', $this->visibleFields);

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

afterEach(function (): void {
    Mockery::close();
});

describe('No action will perform', function (): void {
    it('should not perform any action if the projectable field\'s value is empty', function (): void {
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

    it('should not perform any action if the projectable fields and defined fields are not intersect', function (): void {
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

describe('Throw an exception', function (): void {
    it('should throw an exception if one of projectable fields is invalid', function (): void {
        // Prepare
        $notInVisibleFields = ['email'];
        $projection = new ProjectionField(
            $this->model,
            projectableFields: $notInVisibleFields,
            definedFields: [],
            clientInput: ['fields' => $this->visibleFieldsString],
        );

        // Act & Assert
        expect(fn () => $projection->project())->toThrow(InvalidFieldsException::class);
    });

    it('should throw an exception if the defined fields is empty', function (): void {
        // Prepare
        $projection = new ProjectionField(
            $this->model,
            projectableFields: ['id'],
            definedFields: [],
            clientInput: ['fields' => $this->visibleFieldsString],
        );

        // Act & Assert
        expect(fn () => $projection->project())->toThrow(NoDefinedFieldException::class);
    });

    it('should throw an exception if one of defined fields is invalid', function (): void {
        // Prepare
        $notInVisibleFields = ['email'];
        $projection = new ProjectionField(
            $this->model,
            projectableFields: ['id'],
            definedFields: $notInVisibleFields,
            clientInput: ['fields' => $this->visibleFieldsString],
        );

        // Act & Assert
        expect(fn () => $projection->project())->toThrow(InvalidFieldsException::class);
    });
});

describe('Valid scenarios', function (): void {
    it('should passed all valid scenarios for client input "fields"', function ($projectableFields, $definedFields, $clientInput, $resultFields, $resultFieldsNot): void {
        // Prepare
        $projection = new ProjectionField(
            $this->model,
            $projectableFields,
            $definedFields,
            ['fields' => $clientInput]
        );

        $projectionNot = new ProjectionFieldNot(
            $this->model,
            $projectableFields,
            $definedFields,
            ['fields!' => $clientInput]
        );

        // Act & Assert
        expect($projection->project())->toBe($resultFields);
        expect($projectionNot->project())->toBe($resultFieldsNot);
    })
        ->with('projection-truth-table');
});
