<?php

namespace Laradigs\Tweaker\V32\Projection;

use Laradigs\Tweaker\V32\ValueObjects\LinearArray;

class Intersect extends Projection
{
    protected function project(LinearArray $items)
    {
        return array_intersect($this->items->get(), $items->get());
    }
}
