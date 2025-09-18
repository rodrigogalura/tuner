<?php

use Tuner\TunerBuilder;
use Tuner\Exceptions\TunerException;
use Illuminate\Database\Eloquent\Builder;

it('should thrown an exception when creating tuner builder more than one.', function (): void {
    $builder = Mockery::mock(Builder::class);

    TunerBuilder::create($builder, []);
    TunerBuilder::create($builder, []);
})->throws(TunerException::class);
