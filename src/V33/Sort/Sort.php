<?php

namespace Laradigs\Tweaker\V33\Sort;

use Laradigs\Tweaker\V33\ValueObjects\Columns;
use Laradigs\Tweaker\V33\ValueObjects\SortableColumns;

class Sort
{
    public function __construct(
        protected SortableColumns $sCols,
        protected Columns $cols
    ) {
        logger()->debug(print_r($pCols->getParsedColumns(), true));
        logger()->debug(print_r($cols->getParsedColumns(), true));
    }
}
