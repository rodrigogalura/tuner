<?php

use Tuner\V33\ValueObjects\Requests\SearchRequest;

describe('Search Request', function (): void {
    it('should thrown an exception when searchable columns are empty.', function ($searchKeyword): void {
        // Prepare
        $config = [
            'key' => 'search',
            'minimum_length' => 2,
        ];

        $request = [
            'search' => ['foo' => $searchKeyword],
        ];

        $visibleColumns = ['foo'];
        $searchableColumns = [];

        // Act & Assert
        new SearchRequest($config, $request, $visibleColumns, $searchableColumns);
    })
        ->with(['tuner', '*tuner*', '*tuner', 'tuner*'])
        ->throws(Exception::class);

    it('should thrown an exception when all searchable columns are not in visible columns.', function ($searchKeyword): void {
        // Prepare
        $config = [
            'key' => 'search',
            'minimum_length' => 2,
        ];

        $request = [
            'search' => ['foo' => $searchKeyword],
        ];

        $visibleColumns = ['foo', 'bar'];
        $searchableColumns = ['baz'];

        // Act & Assert
        new SearchRequest($config, $request, $visibleColumns, $searchableColumns);
    })
        ->with(['tuner', '*tuner*', '*tuner', 'tuner*'])
        ->throws(Exception::class);

    it('should thrown an exception when request value is not array.', function ($requestValue): void {
        // Prepare
        $config = [
            'key' => 'search',
            'minimum_length' => 2,
        ];

        $request = [
            'search' => $requestValue,
        ];

        // Act & Assert
        new SearchRequest($config, $request, ['foo'], ['*']);
    })
        ->with([1, 'foo'])
        ->throws(Exception::class);

    it('should thrown an exception the search has more than one size.', function ($searchKeyword): void {
        // Prepare
        $config = [
            'key' => 'search',
            'minimum_length' => 2,
        ];

        $request = [
            'search' => [
                'foo' => $searchKeyword,
                'bar' => $searchKeyword,
            ],
        ];

        $visibleColumns = ['foo', 'bar'];
        $searchableColumns = ['baz'];

        // Act & Assert
        new SearchRequest($config, $request, $visibleColumns, $searchableColumns);
    })
        ->with(['tuner', '*tuner*', '*tuner', 'tuner*'])
        ->throws(Exception::class);

    it('should thrown an exception when requesting non-existing columns.', function ($searchKeyword): void {
        // Prepare
        $config = [
            'key' => 'search',
            'minimum_length' => 2,
        ];

        $request = [
            'search' => ['baz' => $searchKeyword],
        ];

        $visibleColumns = ['foo', 'bar'];
        $searchableColumns = ['*'];

        // Act & Assert
        new SearchRequest($config, $request, $visibleColumns, $searchableColumns);
    })
        ->with(['tuner', '*tuner*', '*tuner', 'tuner*'])
        ->throws(Exception::class);

    it('should thrown an exception when the search keyword not meet the required length.', function ($searchKeyword): void {
        // Prepare
        $config = [
            'key' => 'search',
            'minimum_length' => 100,
        ];

        $request = [
            'search' => ['bar' => $searchKeyword],
        ];

        $visibleColumns = ['foo', 'bar'];
        $searchableColumns = ['*'];

        // Act & Assert
        new SearchRequest($config, $request, $visibleColumns, $searchableColumns);
    })
        ->with(['tuner', '*tuner*', '*tuner', 'tuner*'])
        ->throws(Exception::class);

    test('should get request value of search modifier', function ($searchKeyword, $interpret): void {
        // Prepare
        $config = [
            'key' => 'search',
            'minimum_length' => 2,
        ];

        $request = [
            'search' => ['baz' => $searchKeyword],
        ];

        $visibleColumns = ['foo', 'bar', 'baz'];
        $searchableColumns = ['*'];

        // Act & Assert
        $request = new SearchRequest($config, $request, $visibleColumns, $searchableColumns);
        expect($request())->toBe(['search' => ['baz' => $interpret]]);
    })
        ->with([
            ['searchKeyword' => 'tuner', 'interpret' => '%tuner%'],
            ['searchKeyword' => '*tuner*', 'interpret' => '%tuner%'],
            ['searchKeyword' => '*tuner', 'interpret' => '%tuner'],
            ['searchKeyword' => 'tuner*', 'interpret' => 'tuner%'],
        ]);
});
