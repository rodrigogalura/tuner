<?php

namespace RodrigoGalura\Tuner\V33\Projection;

class ExceptProjection extends Projection
{
    public function project()
    {
        return array_diff(
            array_intersect(($this->projectableColumns)(), ($this->definedColumns)()),
            $this->columns
        );
    }
}
