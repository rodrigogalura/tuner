<?php

namespace Laradigs\Tweaker\V31\TruthTable;

use function RGalura\ApiIgniter\filter_explode;

abstract class TruthTable
{
    protected const RULE_PASSED_CODE = 1;

    public function __construct(
        private array $items = []
    )
    {
        //
    }

    protected function extractIfAsterisk(string &$str)
    {
        if (trim($str) === '*') {
            $str = implode(', ', $this->items);
        }
    }

    protected function someFirstNotInSecond(array $first, array $second)
    {
        return ! empty (array_diff($first, $second));
    }

    protected function intersectAllKeys(array $keys)
    {
        while (count($keys) > 1) {
            $p = filter_explode(array_shift($keys));
            $q = filter_explode(array_shift($keys));

            $keys[0] = implode(', ', $this->intersect($p, $q));

            next($keys);
        }

        return array_shift($keys);
    }

    protected function intersect(array $p, array $q)
    {
        return array_values(array_intersect($p, $q));
    }

    protected function except(array $p, array $q)
    {
        return array_values(array_diff($p, $q));
    }

    abstract public function truthTable(array $matrix2D);

    // public function matrix2d(array $variables)
    // {
    //     if (count($variables) === 0) {
    //         return [];
    //     }

    //     if (count($variables) > 1) {
    //         $arr = [];

    //         $variable = array_shift($variables);

    //         foreach ($variable as $value) {
    //             $currentVariable = $this->matrix2d($variables);

    //             foreach ($currentVariable as $currentValue) {
    //                 $arr[] = array_merge([$value], is_array($currentValue) ? $currentValue : [$currentValue]);
    //             }
    //         }

    //         return $arr;
    //     }

    //     return array_shift($variables);
    // }
}
