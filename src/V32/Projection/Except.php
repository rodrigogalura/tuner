<?php

namespace Laradigs\Tweaker\V32\Projection;

use Laradigs\Tweaker\V32\ValueObjects\LinearArray;

class Except extends Projection
{
    protected function project(LinearArray $items)
    {
        return array_diff($this->items->get(), $items->get());
    }
}
