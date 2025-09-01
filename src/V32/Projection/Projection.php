<?php

namespace Laradigs\Tweaker\V32\Projection;

use Laradigs\Tweaker\V32\ValueObjects\LinearArray;

abstract class Projection
{
    private function __construct(
        protected LinearArray $items
    ) {
        //
    }

    public static function from(array $items)
    {
        return new static(new LinearArray($items));
    }

    public function to(array $items)
    {
        $this->projectedItems = $this->project(new LinearArray($items));

        return $this;
    }

    public function get()
    {
        return $this->projectedItems;
    }

    public function count()
    {
        return count($this->get());
    }

    public function isEmpty()
    {
        return $this->count() === 0;
    }

    public function isNotEmpty()
    {
        return ! $this->isEmpty();
    }

    public function toR(array &$items)
    {
        $items = $this->to($items)->get();
    }

    abstract protected function project(LinearArray $items);
}
