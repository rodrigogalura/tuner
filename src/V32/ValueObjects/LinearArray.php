<?php

namespace Laradigs\Tweaker\V32\ValueObjects;

class LinearArray
{
    public function __construct(protected array $items)
    {
        if (! $this->isLinear()) {
            throw new \InvalidArgumentException('Invalid items. Array items must be a linear array.');
        }
    }

    private function isLinear()
    {
        while ($currentItem = current($this->items)) {
            if (is_array($currentItem)) {
                return false;
            }

            next($this->items);
        }

        return true;
    }

    public function get()
    {
        return $this->items;
    }
}
