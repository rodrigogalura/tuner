<?php

namespace Laradigs\Tweaker\V33\Projection;

class IntersectProjection extends Projection
{
    public function project()
    {
        return array_intersect($this->pCols->getParsedColumns(), $this->cols->getParsedColumns());
    }
}
