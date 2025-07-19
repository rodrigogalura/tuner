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

    private function extractAndValidate(string|array &$value)
    {
        if ($value === '*' || $value === ['*']) {
            $value = $this->allItems;
        }

        if (is_string($value)) {
            $value = filter_explode($value);
        }

        return empty($this->diffFromAllItems($value));
    }

    private function diffFromAllItems(array $fields)
    {
        return $this->diff($fields, $this->allItems);
    }

    private function diff(array $p, array $q)
    {
        return array_values(array_diff($p, $q));
    }

    public function intersect(array|string $p, array|string $q)
    {
        return $this->extractAndValidate($p) && $this->extractAndValidate($q)
            ? array_values(array_intersect($p, $q))
            : false;
    }

    public function except(array|string $p, array|string $q)
    {
        return $this->extractAndValidate($p) && $this->extractAndValidate($q)
            ? array_values(array_diff($p, $q))
            : false;
    }
}
