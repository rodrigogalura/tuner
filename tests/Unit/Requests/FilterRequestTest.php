<?php

use Tuner\Exceptions\ClientException;
use Tuner\Exceptions\TunerException;
use Tuner\Fields\FilterableFields;
use Tuner\Requests\FilterRequest;

beforeEach(function (): void {
    $this->config = ['key' => array_combine($keys = ['filter', 'in', 'between'], $keys)];
});

describe('Filter Request', function (): void {
    it('should thrown an exception when filterable fields are empty.', function (): void {
        // Prepare
        $request = ['filter' => ['foo' => 'fooVal']];

        // Act & Assert
        new FilterRequest($request, $this->config, visibleFields: ['foo'], filterableFields: []);
    })->throws(
        TunerException::class,
        exceptionCode: FilterableFields::ERR_CODE_DISABLED
    );

    it('should thrown an exception when all filterable fields are not in visible fields.', function (): void {
        // Prepare
        $request = ['filter' => ['foo' => 'fooVal']];

        // Act & Assert
        new FilterRequest($request, $this->config, visibleFields: ['foo', 'bar'], filterableFields: ['baz']);
    })->throws(
        TunerException::class,
        exceptionCode: FilterableFields::ERR_CODE_PCOLS_VCOLS_NO_MATCH
    );

    it('should thrown an exception when request value is not array.', function ($requestValue): void {
        // Prepare
        $request = ['filter' => $requestValue];

        // Act & Assert
        new FilterRequest($request, $this->config, visibleFields: ['foo'], filterableFields: ['*']);
    })
        ->with([1, 'foo'])
        ->throws(ClientException::class);

    it('should thrown an exception when logic fields are invalid.', function ($requestValue): void {
        // Prepare
        $request = ['filter' => $requestValue];

        // Act & Assert
        new FilterRequest($request, $this->config, visibleFields: ['foo', 'bar'], filterableFields: ['*']);
    })
        ->with([
            ['requestValue' => ['foo and bar' => 'foobar']],
            ['requestValue' => ['foo or bar' => 'foobar']],
        ])
        ->throws(ClientException::class);

    it('should thrown an exception when requesting non-existing fields.', function (): void {
        // Prepare
        $request = ['filter' => ['baz' => 'asc']];

        // Act & Assert
        new FilterRequest($request, $this->config, visibleFields: ['foo', 'bar'], filterableFields: ['*']);
    })
        ->throws(ClientException::class);

    test('should get request value of filter modifier', function (): void {
        // Prepare
        $request = ['filter' => ['baz' => 'bazVal']];

        // Act & Assert
        $request = new FilterRequest($request, $this->config, visibleFields: ['foo', 'bar', 'baz'], filterableFields: ['*']);
        expect($request())->toBe([
            'filter' => [
                ['AND', 'baz', false, '=', 'bazVal'],
            ],
        ]);
    });
});
