<?php

use Illuminate\Database\Eloquent\Model;
use Laradigs\Tweaker\Projection\NoActionWillPerformException;
use Laradigs\Tweaker\Searching\Searching;

beforeEach(function (): void {
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

afterEach(function (): void {
    Mockery::close();
});

describe('Not perform any action. Just return defined value as default.', function (): void {
    it('should not perform any action if the search "fields" are empty', function (): void {
        // Prepare
        $searching = new Searching(
            $this->model,
            searchableFields: $this->visibleFields,
            clientInput: ['' => 'foo bar'],
        );

        // Act & Assert
        expect(fn () => $searching->search())->toThrow(NoActionWillPerformException::class);
    });

    it('should not perform any action if the search "value" is empty', function (): void {
        // Prepare
        $searching = new Searching(
            $this->model,
            searchableFields: $this->visibleFields,
            clientInput: ['name' => ''],
        );

        // Act & Assert
        expect(fn () => $searching->search())->toThrow(NoActionWillPerformException::class);
    });

    it('should not perform any action if the search "value" length not hit the minimum', function (): void {
        $MINIMUM_LENGTH = 5;
        $searchKeyword = str_repeat('A', $MINIMUM_LENGTH - 1);

        // Prepare
        $searching = new Searching(
            $this->model,
            searchableFields: $this->visibleFields,
            clientInput: ['name' => $searchKeyword],
            minimumLength: $MINIMUM_LENGTH
        );

        // Act & Assert
        expect(fn () => $searching->search())->toThrow(NoActionWillPerformException::class);
    });

    it('should not perform any action if the searchable fields are empty', function (): void {
        // Prepare
        $searching = new Searching(
            $this->model,
            searchableFields: [],
            clientInput: ['name' => 'foo'],
        );

        // Act & Assert
        expect(fn () => $searching->search())->toThrow(NoActionWillPerformException::class);
    });
});

// describe('Throw an exception', function (): void {
//     it('should throw an exception if one of projectable fields is invalid', function (): void {
//         // Prepare
//         $notInVisibleFields = ['email'];
//         $projection = new Searching(
//             $this->model,
//             searchableFields: $notInVisibleFields,
//             definedFields: [],
//             clientInput: $this->visibleFields,
//         );

//         // Act & Assert
//         expect(fn () => $projection->project())->toThrow(InvalidFieldsException::class);
//     });

//     it('should throw an exception if the defined fields is empty', function (): void {
//         // Prepare
//         $projection = new Searching(
//             $this->model,
//             searchableFields: ['id'],
//             definedFields: [],
//             clientInput: $this->visibleFields,
//         );

//         // Act & Assert
//         expect(fn () => $projection->project())->toThrow(NoDefinedFieldException::class);
//     });

//     it('should throw an exception if one of defined fields is invalid', function (): void {
//         // Prepare
//         $notInVisibleFields = ['email'];
//         $projection = new Searching(
//             $this->model,
//             searchableFields: ['id'],
//             definedFields: $notInVisibleFields,
//             clientInput: $this->visibleFields,
//         );

//         // Act & Assert
//         expect(fn () => $projection->project())->toThrow(InvalidFieldsException::class);
//     });
// });

// describe('Valid scenarios', function (): void {
//     it('should passed all valid scenarios for client input "fields"', function ($searchableFields, $definedFields, $clientInput, $expectedResult): void {
//         // Prepare
//         $projection = new Searching(
//             $this->model,
//             $searchableFields,
//             $definedFields,
//             filter_explode($clientInput)
//         );

//         // Act & Assert
//         expect($projection->project())->toBe($expectedResult);
//     })
//         ->with('fields-truth-table');

//     it('should passed all valid scenarios for client input "fields!"', function ($searchableFields, $definedFields, $clientInput, $expectedResult): void {
//         // Prepare
//         $projection = new Searching(
//             $this->model,
//             $searchableFields,
//             $definedFields,
//             filter_explode($clientInput)
//         );

//         // Act & Assert
//         expect($projection->project())->toBe($expectedResult);
//     })
//         ->with('fields-not-truth-table');
// });
