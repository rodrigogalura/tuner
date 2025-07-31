<?php

namespace Laradigs\Tweaker\V31;

use function RGalura\ApiIgniter\filter_explode;

class TruthTable
{
    public function __construct(
        private array $rules
    )
    {
        // $this->variables = $variables;
    }

    private function someFirstNotInSecond(array $first, array $second)
    {
        return ! empty (array_diff($first, $second));
    }

    private function intersect(array $p, array $q)
    {
        return array_values(array_intersect($p, $q));
    }

    // private function intersectStrict(array $p, array $q, int $errorCode)
    // {
    //     return $this->someFirstNotInSecond($q, $p)
    //         ? [$errorCode]
    //         : $this->intersect($p, $q);
    // }

    private function except(array $p, array $q)
    {
        return array_values(array_diff($p, $q));
    }

    // private function exceptStrict(array $p, array $q, int $errorCode)
    // {
    //     return $this->someFirstNotInSecond($q, $p)
    //         ? [$errorCode]
    //         : $this->except($p, $q);
    // }

    // original code
    // public function matrix($array, $keys = [])
    // {
    //     $arr = [];

    //     if (count($array) >= 2) {
    //         $pick = array_shift($array);

    //         foreach ($pick as $value) {
    //             $arr[$value] = $this->matrix($array, array_merge($keys, [$value]));
    //         }

    //         return $arr;
    //     }

    //     $last = array_shift($array);

    //     foreach ($last as $value) {
    //         $arr[$value] = implode(' intersect ', array_merge($keys, [$value]));
    //     }

    //     return $arr;
    // }

    private function intersectAllKeys(array $keys)
    {
        while (count($keys) >= 2) {
            $p = filter_explode(array_shift($keys));
            $q = filter_explode(array_shift($keys));

            $keys[0] = implode(', ', $this->intersect($p, $q));

            next($keys);
        }

        return array_shift($keys);
    }

    // refactor recursion
    public function matrix(array $variables, $keys = [])
    {
        $recursion = function($variablesAlias, $keys, $cb) {
            $arr = [];

            $pick = array_shift($variablesAlias);

            foreach ($pick as $value) {
                $arr[$value] = $cb($variablesAlias, $keys, $value);
            }

            return $arr;
        };

        if (count($variables) >= 2) {
            return $recursion($variables, $keys, function($variablesAlias, $keys, $value) {
                return $this->matrix($variablesAlias, array_merge($keys, [$value]));
            });
        }

        return $recursion($variables, $keys, function($variablesAlias, $keys, $value) {
            $compressedKey = $this->intersectAllKeys($keys);

            $p = filter_explode($compressedKey);
            $q = filter_explode($value);

            $some = $this->someFirstNotInSecond($q, $p);

            $intersect = implode(', ', $this->intersect($p, $q));
            $intersect_strict = $some ? 422 : $intersect;

            $except = implode(', ', $this->except($p, $q));
            $except_strict = $some ? 422 : $except;

            return compact('intersect', 'intersect_strict', 'except', 'except_strict');
        });
    }
}
