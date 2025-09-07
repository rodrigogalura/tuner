<?php

namespace RodrigoGalura\Tuner\V33\Sort;

use RodrigoGalura\Tuner\V33\ValueObjects\Columns;
use RodrigoGalura\Tuner\V33\ValueObjects\SortableColumns;

class Sort
{
    public function __construct(
        protected SortableColumns $sortableColumns,
        protected Columns $columns
    ) {
        logger()->debug(print_r($sortableColumns->getParsedColumns(), true));
        logger()->debug(print_r($columns->getParsedColumns(), true));
    }
}
