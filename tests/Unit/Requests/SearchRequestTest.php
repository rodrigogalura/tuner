<?php

use Tuner\Exceptions\ClientException;
use Tuner\Exceptions\TunerException;
use Tuner\Fields\SearchableFields;
use Tuner\Requests\SearchRequest;

beforeEach(function (): void {
    $this->config = [
        'key' => 'search',
        'minimum_length' => 2,
    ];
});

describe('Search Request', function (): void {
    it('should thrown an exception when searchable fields are empty.', function ($searchKeyword): void {
        // Prepare
        $request = ['search' => ['foo' => $searchKeyword]];

        // Act & Assert
        new SearchRequest($request, $this->config, visibleFields: ['foo'], searchableFields: []);
    })
        ->with([
            'match anywhere' => 'tuner',
            'match anywhere too' => '*tuner*',
            'match at the beginning' => 'tuner*',
            'match at the end' => '*tuner',
        ])
        ->throws(
            TunerException::class,
            exceptionCode: SearchableFields::ERR_CODE_DISABLED
        );

    it('should thrown an exception when all searchable fields are not in visible fields.', function ($searchKeyword): void {
        // Prepare
        $request = ['search' => ['foo' => $searchKeyword]];

        // Act & Assert
        new SearchRequest($request, $this->config, visibleFields: ['foo', 'bar'], searchableFields: ['baz']);
    })
        ->with([
            'match anywhere' => 'tuner',
            'match anywhere too' => '*tuner*',
            'match at the beginning' => 'tuner*',
            'match at the end' => '*tuner',
        ])
        ->throws(
            TunerException::class,
            exceptionCode: SearchableFields::ERR_CODE_PCOLS_VCOLS_NO_MATCH
        );

    it('should thrown an exception when request value is not array.', function ($requestValue): void {
        // Prepare
        $request = ['search' => $requestValue];

        // Act & Assert
        new SearchRequest($request, $this->config, visibleFields: ['foo'], searchableFields: ['*']);
    })
        ->with([1, 'foo'])
        ->throws(ClientException::class);

    it('should thrown an exception the search has more than one size.', function ($searchKeyword): void {
        // Prepare
        $request = [
            'search' => [
                'foo' => $searchKeyword,
                'bar' => $searchKeyword,
            ],
        ];

        // Act & Assert
        new SearchRequest($request, $this->config, visibleFields: ['foo', 'bar'], searchableFields: ['*']);
    })
        ->with([
            'match anywhere' => 'tuner',
            'match anywhere too' => '*tuner*',
            'match at the beginning' => 'tuner*',
            'match at the end' => '*tuner',
        ])
        ->throws(ClientException::class);

    it('should thrown an exception when requesting non-existing fields.', function ($searchKeyword): void {
        // Prepare
        $request = ['search' => ['baz' => $searchKeyword]];

        // Act & Assert
        new SearchRequest($request, $this->config, visibleFields: ['foo', 'bar'], searchableFields: ['*']);
    })
        ->with([
            'match anywhere' => 'tuner',
            'match anywhere too' => '*tuner*',
            'match at the beginning' => 'tuner*',
            'match at the end' => '*tuner',
        ])
        ->throws(ClientException::class);

    it('should thrown an exception when the search keyword not meet the required length.', function ($searchKeyword): void {
        // Prepare
        $config = [
            'key' => 'search',
            'minimum_length' => 100,
        ];

        $request = ['search' => ['bar' => $searchKeyword]];

        // Act & Assert
        new SearchRequest($request, $config, visibleFields: ['foo', 'bar'], searchableFields: ['*']);
    })
        ->with([
            'match anywhere' => 'tuner',
            'match anywhere too' => '*tuner*',
            'match at the beginning' => 'tuner*',
            'match at the end' => '*tuner',
        ])
        ->throws(ClientException::class);

    test('should get request value of search modifier', function ($searchKeyword, $interpret): void {
        // Prepare
        $request = ['search' => ['baz' => $searchKeyword]];

        // Act & Assert
        $request = new SearchRequest($request, $this->config, visibleFields: ['foo', 'bar', 'baz'], searchableFields: ['*']);
        expect($request())->toBe(['search' => ['baz' => $interpret]]);
    })
        ->with([
            ['searchKeyword' => 'tuner', 'interpret' => '%tuner%'],
            ['searchKeyword' => '*tuner*', 'interpret' => '%tuner%'],
            ['searchKeyword' => '*tuner', 'interpret' => '%tuner'],
            ['searchKeyword' => 'tuner*', 'interpret' => 'tuner%'],
        ]);
});
