<?php

namespace Laradigs\Tweaker;

use function RGalura\ApiIgniter\filter_explode;

class TruthTable
{
    public function __construct(
        private readonly array $allItems
    ) {
        //
    }

    public function diffFromAllItems(string|array &$value)
    {
        if (is_string($value)) {
            $value = filter_explode($value);
        }

        return array_values(array_diff($value, $this->allItems));
    }

    public function extractIfAsterisk(string|array &$value)
    {
        if ($value === '*' || $value === ['*']) {
            $value = $this->allItems;
        }
    }

    public function extractIfKeyIsAsterisk(array &$value)
    {
        if (array_key_exists('*', $value)) {
            $value = array_fill_keys($this->allItems, $value['*']);
        }
    }

    public function intersect(array|string $p, array|string $q)
    {
        $this->extractIfAsterisk($p);
        $this->extractIfAsterisk($q);

        return array_values(array_intersect($p, $q));
    }

    public function except(array|string $p, array|string $q)
    {
        $this->extractIfAsterisk($p);
        $this->extractIfAsterisk($q);

        return array_values(array_diff($p, $q));
    }
}
