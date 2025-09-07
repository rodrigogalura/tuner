<?php

namespace RodrigoGalura\Tuner\V33\Projection;

use RodrigoGalura\Tuner\V33\ValueObjects\DefinedColumns;
use RodrigoGalura\Tuner\V33\ValueObjects\ProjectableColumns;

abstract class Projection implements Projectable
{
    public function __construct(
        protected ProjectableColumns $projectableColumns,
        protected DefinedColumns $definedColumns,
        protected array $columns
    ) {
        logger()->debug(print_r($projectableColumns(), true));
        logger()->debug(print_r($definedColumns(), true));
        logger()->debug(print_r($columns, true));
    }

    abstract public function project();
}
