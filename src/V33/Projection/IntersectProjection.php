<?php

namespace Laradigs\Tweaker\V33\Projection;

class IntersectProjection extends Projection
{
    public function project()
    {
        return array_intersect(
            array_intersect(($this->projectableColumns)(), ($this->definedColumns)()),
            $this->columns
        );
    }
}
