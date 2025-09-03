<?php

namespace Laradigs\Tweaker\V33\Projection;

class IntersectProjection extends Projection
{
    public function project()
    {
        return array_intersect($this->from, $this->to);
    }
}
