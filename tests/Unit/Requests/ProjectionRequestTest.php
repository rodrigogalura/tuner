<?php

use Tuner\Exceptions\ClientException;
use Tuner\Exceptions\TunerException;
use Tuner\Fields\DefinedFields;
use Tuner\Fields\ProjectableFields;
use Tuner\Requests\ProjectionRequest;

beforeEach(function (): void {
    $this->config = [
        'key' => [
            'intersect' => 'fields',
            'except' => 'fields!',
        ],
    ];
});

describe('Projection Request', function (): void {
    it('should thrown an exception when projectable fields are empty.', function (): void {
        // Prepare
        $request = ['fields' => 1];

        // Act & Assert
        new ProjectionRequest($request, $this->config, visibleFields: ['foo'], projectableFields: [], definedFields: ['*']);
    })->throws(
        TunerException::class,
        exceptionCode: ProjectableFields::ERR_CODE_DISABLED
    );

    it('should thrown an exception when all projectable fields are not in visible fields.', function (): void {
        // Prepare
        $request = ['fields' => 1];

        // Act & Assert
        new ProjectionRequest($request, $this->config, visibleFields: ['foo', 'bar'], projectableFields: ['baz'], definedFields: ['*']);
    })->throws(
        TunerException::class,
        exceptionCode: ProjectableFields::ERR_CODE_PCOLS_VCOLS_NO_MATCH
    );

    it('should thrown an exception when defined fields are empty.', function (): void {
        // Prepare
        $request = ['fields' => 1];

        // Act & Assert
        new ProjectionRequest($request, $this->config, visibleFields: ['foo'], projectableFields: ['foo'], definedFields: []);
    })->throws(
        TunerException::class,
        exceptionCode: DefinedFields::ERR_CODE_QUERY_EXCEPTION
    );

    it('should thrown an exception when all defined fields are not in visible fields.', function (): void {
        // Prepare
        $request = ['fields' => 1];

        // Act & Assert
        new ProjectionRequest($request, $this->config, visibleFields: ['foo', 'bar'], projectableFields: ['*'], definedFields: ['baz']);
    })->throws(
        TunerException::class,
        exceptionCode: DefinedFields::ERR_CODE_DCOLS_VCOLS_NO_MATCH
    );

    it('should thrown an exception when using modifier for intersect and except at the same time.', function (): void {
        // Prepare
        $request = [
            'fields' => 'foo',
            'fields!' => 'bar',
        ];

        // Act & Assert
        new ProjectionRequest($request, $this->config, visibleFields: [], projectableFields: [], definedFields: []);
    })->throws(ClientException::class);

    it('should thrown an exception when request value is not string.', function (): void {
        // Prepare
        $request = ['fields' => 1];

        // Act & Assert
        new ProjectionRequest($request, $this->config, visibleFields: ['foo'], projectableFields: ['foo'], definedFields: ['*']);
    })->throws(ClientException::class);

    it('should thrown an exception when requesting non-existing fields.', function (): void {
        // Prepare
        $request = ['fields' => 'baz'];

        // Act & Assert
        new ProjectionRequest($request, $this->config, visibleFields: ['foo', 'bar'], projectableFields: ['*'], definedFields: ['*']);
    })
        ->throws(ClientException::class);

    test('should get request value of fields modifier', function ($fields, $expected): void {
        // Prepare
        $request = compact('fields');

        // Act & Assert
        $request = new ProjectionRequest($request, $this->config, visibleFields: ['foo', 'bar', 'baz'], projectableFields: ['*'], definedFields: ['*']);
        expect($request())->toBe(['fields' => $expected]);
    })->with([
        ['fields' => 'baz', 'expected' => ['baz']],
        ['fields' => 'foo,bar', 'expected' => ['foo', 'bar']],
        ['fields' => 'foo, bar', 'expected' => ['foo', 'bar']],
        ['fields' => 'foo,baz', 'expected' => ['foo', 'baz']],
        ['fields' => 'foo, baz', 'expected' => ['foo', 'baz']],
    ]);

    test('should get request value of fields! modifier', function (): void {
        // Prepare
        $request = ['fields!' => 'baz'];

        // Act & Assert
        $request = new ProjectionRequest($request, $this->config, visibleFields: ['foo', 'bar', 'baz'], projectableFields: ['*'], definedFields: ['*']);
        expect($request())->toBe(['fields!' => ['foo', 'bar']]);
    });
});
