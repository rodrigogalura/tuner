<?php

use Illuminate\Database\Eloquent\Builder;
use Tuner\Exceptions\TunerException;
use Tuner\TunerBuilder;

it('should thrown an exception when creating tuner builder more than one.', function (): void {
    $builder = Mockery::mock(Builder::class);

    TunerBuilder::create($builder, []);
    TunerBuilder::create($builder, []);
})->throws(TunerException::class, exceptionCode: TunerBuilder::ERR_CODE_MULTIPLE_BUILDER);
