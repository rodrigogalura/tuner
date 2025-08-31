<?php

namespace Laradigs\Tweaker\V32\Projection;

use Laradigs\Tweaker\V32\ValueObjects\LinearArray;

abstract class Projection
{
    private function __construct(
        protected LinearArray $items
    )
    {
        //
    }

    public static function from(array $items)
    {
        return new static(new LinearArray($items));
    }

    public function to(array $items)
    {
        return $this->project(new LinearArray($items));
    }

    public function toR(array &$items)
    {
        $items = $this->to($items);
    }

    abstract protected function project(LinearArray $items);
}
