<?php

use Illuminate\Database\Eloquent\Model;
use Laradigs\Tweaker\Projection\NoActionWillPerformException;
use Laradigs\Tweaker\Search\Search;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;

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

describe('Not perform any action.', function (): void {
    it('should not perform any action if the search "fields" are empty', function (): void {
        // Prepare
        $search = new Search(
            $this->model,
            searchableFields: $this->visibleFields,
            clientInput: ['' => 'foo bar'],
        );

        // Act & Assert
        expect(fn () => $search->search())->toThrow(NoActionWillPerformException::class);
    });

    it('should not perform any action if one of search "fields" is invalid', function (): void {
        // Prepare
        $search = new Search(
            $this->model,
            searchableFields: $this->visibleFields,
            clientInput: ['email' => 'foo'], // 'email' is not existing on visible fields
        );

        // Act & Assert
        expect(fn () => $search->search())->toThrow(NoActionWillPerformException::class);
    });

    it('should not perform any action if the search "value" is empty', function (): void {
        // Prepare
        $search = new Search(
            $this->model,
            searchableFields: $this->visibleFields,
            clientInput: ['name' => ''],
        );

        // Act & Assert
        expect(fn () => $search->search())->toThrow(NoActionWillPerformException::class);
    });

    it('should not perform any action if the search "value" length not hit the minimum', function (): void {
        $MINIMUM_LENGTH = 5;
        $searchKeyword = str_repeat('A', $MINIMUM_LENGTH - 1);

        // Prepare
        $search = new Search(
            $this->model,
            searchableFields: $this->visibleFields,
            clientInput: ['name' => $searchKeyword],
            minimumLength: $MINIMUM_LENGTH
        );

        // Act & Assert
        expect(fn () => $search->search())->toThrow(NoActionWillPerformException::class);
    });

    it('should not perform any action if the searchable fields are empty', function (): void {
        // Prepare
        $search = new Search(
            $this->model,
            searchableFields: [],
            clientInput: ['name' => 'foo'],
        );

        // Act & Assert
        expect(fn () => $search->search())->toThrow(NoActionWillPerformException::class);
    });
});

describe('Throw an exception', function (): void {
    it('should throw an exception if one of searchable fields is invalid', function (): void {
        // Prepare
        $search = new Search(
            $this->model,
            searchableFields: ['email'], // not existing on visible fields
            clientInput: ['name' => 'foo'],
        );

        // Act & Assert
        expect(fn () => $search->search())->toThrow(InvalidFieldsException::class);
    });
});

describe('Valid scenarios', function (): void {
    it('should passed all valid scenarios', function ($searchableFields, $clientFields, $resultFields, $clientKeyword, $resultKeyword): void {
        dump($searchableFields, $clientFields, $resultFields, $clientKeyword, $resultKeyword);
        // Prepare
        $search = new Search(
            $this->model,
            searchableFields: $searchableFields,
            clientInput: [$clientFields => $clientKeyword],
        );

        // Act & Assert
        expect($search->search())->toBe([$resultFields => $resultKeyword]);
    })
        ->with('search-fields-truth-table')
        ->with('search-keyword-truth-table');
});
