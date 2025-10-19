<?php

use Tuner\Columns\SortableColumns;
use Tuner\Exceptions\ClientException;
use Tuner\Exceptions\TunerException;
use Tuner\Requests\SortRequest;

beforeEach(function (): void {
    $this->config = [
        'key' => 'sort',
    ];
});

describe('Sort Request', function (): void {
    it('should thrown an exception when sortable columns are empty.', function (): void {
        // Prepare
        $request = ['sort' => ['foo' => 'asc']];

        // Act & Assert
        new SortRequest($request, $this->config, visibleColumns: ['foo'], sortableColumns: []);
    })->throws(
        TunerException::class,
        exceptionCode: SortableColumns::ERR_CODE_DISABLED
    );

    it('should thrown an exception when all sortable columns are not in visible columns.', function (): void {
        // Prepare
        $request = ['sort' => ['foo' => 'asc']];

        // Act & Assert
        new SortRequest($request, $this->config, visibleColumns: ['foo', 'bar'], sortableColumns: ['baz']);
    })->throws(
        TunerException::class,
        exceptionCode: SortableColumns::ERR_CODE_PCOLS_VCOLS_NO_MATCH
    );

    it('should thrown an exception when request value is not array.', function ($requestValue): void {
        // Prepare
        $request = ['sort' => $requestValue];

        // Act & Assert
        new SortRequest($request, $this->config, ['foo'], ['*']);
    })
        ->with([1, 'foo'])
        ->throws(ClientException::class);

    it('should thrown an exception when requesting non-existing columns.', function (): void {
        // Prepare
        $request = ['sort' => ['baz' => 'asc']];

        // Act & Assert
        new SortRequest($request, $this->config, visibleColumns: ['foo', 'bar'], sortableColumns: ['*']);
    })
        ->throws(ClientException::class);

    test('should get request value of sort modifier', function (): void {
        // Prepare
        $request = ['sort' => ['baz' => 'asc']];

        // Act & Assert
        $request = new SortRequest($request, $this->config, visibleColumns: ['foo', 'bar', 'baz'], sortableColumns: ['*']);
        expect($request())->toBe(['sort' => ['baz' => 'asc']]);
    });
});
