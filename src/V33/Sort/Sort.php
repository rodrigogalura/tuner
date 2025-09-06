<?php

namespace Laradigs\Tweaker\V33\Sort;

use Laradigs\Tweaker\V33\ValueObjects\Columns;
use Laradigs\Tweaker\V33\ValueObjects\SortableColumns;

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
