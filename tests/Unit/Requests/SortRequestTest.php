<?php

use Tuner\Exceptions\ClientException;
use Tuner\Exceptions\TunerException;
use Tuner\Fields\SortableFields;
use Tuner\Requests\SortRequest;

beforeEach(function (): void {
    $this->config = [
        'key' => 'sort',
    ];
});

describe('Sort Request', function (): void {
    it('should thrown an exception when sortable fields are empty.', function (): void {
        // Prepare
        $request = ['sort' => ['foo' => 'asc']];

        // Act & Assert
        new SortRequest($request, $this->config, visibleFields: ['foo'], sortableFields: []);
    })->throws(
        TunerException::class,
        exceptionCode: SortableFields::ERR_CODE_DISABLED
    );

    it('should thrown an exception when all sortable fields are not in visible fields.', function (): void {
        // Prepare
        $request = ['sort' => ['foo' => 'asc']];

        // Act & Assert
        new SortRequest($request, $this->config, visibleFields: ['foo', 'bar'], sortableFields: ['baz']);
    })->throws(
        TunerException::class,
        exceptionCode: SortableFields::ERR_CODE_PCOLS_VCOLS_NO_MATCH
    );

    it('should thrown an exception when request value is not array.', function ($requestValue): void {
        // Prepare
        $request = ['sort' => $requestValue];

        // Act & Assert
        new SortRequest($request, $this->config, ['foo'], ['*']);
    })
        ->with([1, 'foo'])
        ->throws(ClientException::class);

    it('should thrown an exception when requesting non-existing fields.', function (): void {
        // Prepare
        $request = ['sort' => ['baz' => 'asc']];

        // Act & Assert
        new SortRequest($request, $this->config, visibleFields: ['foo', 'bar'], sortableFields: ['*']);
    })
        ->throws(ClientException::class);

    test('should get request value of sort modifier', function (): void {
        // Prepare
        $request = ['sort' => ['baz' => 'asc']];

        // Act & Assert
        $request = new SortRequest($request, $this->config, visibleFields: ['foo', 'bar', 'baz'], sortableFields: ['*']);
        expect($request())->toBe(['sort' => ['baz' => 'asc']]);
    });
});
