<?php

namespace Laradigs\Tweaker\V33\Projection;

class ExceptProjection extends Projection
{
    public function project()
    {
        return array_diff($this->pCols->getParsedColumns(), $this->cols->getParsedColumns());
    }
}
