<?php

namespace Laradigs\Tweaker\V33\Projection;

class ExceptProjection extends Projection
{
    public function project()
    {
        return array_diff(
            array_intersect($this->pCols->getParsedColumns(), $this->dCols->getParsedColumns()),
            $this->cols->getParsedColumns()
        );
    }
}
