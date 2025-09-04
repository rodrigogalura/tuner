<?php

namespace Laradigs\Tweaker\V33\Projection;

use Laradigs\Tweaker\V33\ValueObjects\Columns;
use Laradigs\Tweaker\V33\ValueObjects\ProjectableColumns;

abstract class Projection implements Projectable
{
    public function __construct(
        protected ProjectableColumns $pCols,
        protected Columns $cols
    )
    {
        logger()->debug(print_r($pCols->getParsedColumns(), true));
        logger()->debug(print_r($cols->getParsedColumns(), true));
    }

    abstract public function project();
}
