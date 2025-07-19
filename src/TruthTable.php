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

    // private function extractAndValidate(string|array &$value)
    // {
    //     $this->extractIfAsterisk($value);
    //     return $this->validate($value);
    // }

    // private function diff(array $p, array $q)
    // {
    //     return array_values(array_diff($p, $q));
    // }

    // public function validateAndIntersect(array|string $p, array|string $q)
    // {
    //     $this->extractAndValidate($p) && $this->extractAndValidate($q)
    //         ? $this->intersect($p, $q)
    //         : false;
    // }

    // public function validateAndExcept(array|string $p, array|string $q)
    // {
    //     $this->extractAndValidate($p) && $this->extractAndValidate($q)
    //         ? $this->except($p, $q)
    //         : false;
    // }

    public function intersect(array|string $p, array|string $q)
    {
        $this->extractIfAsterisk($p);
        $this->extractIfAsterisk($q);

        return array_values(array_intersect($p, $q));

        // return $this->containsAll($p) && $this->containsAll($q)
        //     ? array_values(array_intersect($p, $q))
        //     : false;
    }

    public function except(array|string $p, array|string $q)
    {
        $this->extractIfAsterisk($p);
        $this->extractIfAsterisk($q);

        return array_values(array_diff($p, $q));

        // return $this->containsAll($p) && $this->containsAll($q)
        //     ? array_values(array_diff($p, $q))
        //     : false;
    }
}
