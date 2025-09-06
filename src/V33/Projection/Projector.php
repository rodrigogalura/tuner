<?php

namespace Laradigs\Tweaker\V33\Projection;

class Projector
{
    public function __construct(private Projectable $projection)
    {
        //
    }

    public function getProjectedColumns()
    {
        switch ($this->projection::class) {
            case IntersectProjection::class:
            case ExceptProjection::class:
                return $this->projection->project();

            default:
                throw new \InvalidArgumentException('Unexpected argument '.$p::class);
        }
    }
}
