<?php

namespace Laradigs\Tweaker\V31\TruthTable;

use function RGalura\ApiIgniter\is_multi_array;
use function RGalura\ApiIgniter\filter_explode;

class TruthTable
{
    public function __construct(
        private array $rules = [],
        private array $asteriskValues = []
    )
    {
        // $this->variables = $variables;
    }

    private function someFirstNotInSecond(array $first, array $second)
    {
        return ! empty (array_diff($first, $second));
    }

    // private function intersectStrict(array $p, array $q, int $errorCode)
    // {
    //     return $this->someFirstNotInSecond($q, $p)
    //         ? [$errorCode]
    //         : $this->intersect($p, $q);
    // }

    // private function exceptStrict(array $p, array $q, int $errorCode)
    // {
    //     return $this->someFirstNotInSecond($q, $p)
    //         ? [$errorCode]
    //         : $this->except($p, $q);
    // }

    private function extractIfAsterisk(string &$str)
    {
        if (trim($str) === '*') {
            $str = implode(', ', $this->asteriskValues);
        }
    }

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
            # Extract if asterisk
            for ($i = 0; $i < count($keys); $i++) {
                $this->extractIfAsterisk($keys[$i]);
            }
            $this->extractIfAsterisk($value);

            # Apply the Rules
            foreach ($this->rules as $index => $rules) {
                foreach ($rules as $rule) {
                    if (!is_null($subjectToValidate = $keys[$index] ?? (count($this->rules)-1 == $index ? $value : null))) {
                        if ($rule->handle($subjectToValidate)) {
                            $intersect
                                = $intersect_strict
                                = $except
                                = $except_strict
                                = $rule->getErrorCode();
                            goto end;
                            return;
                        }
                    }
                }
            }

            # Perform intersect and except logic
            $compressedKey = $this->intersectAllKeys($keys);

            $p = filter_explode($compressedKey);
            $q = filter_explode($value);

            $some = $this->someFirstNotInSecond($q, $p);

            $intersect = implode(', ', $this->intersect($p, $q));
            $intersect_strict = $some ? 422 : $intersect;

            $except = implode(', ', $this->except($p, $q));
            $except_strict = $some ? 422 : $except;

            end:
            return compact('intersect', 'intersect_strict', 'except', 'except_strict');
        });
    }

    public function export($filename, array $variables)
    {
        $handle = fopen($filename, 'w');

        fputcsv($handle, ['Truth Table']);
        fputcsv($handle, array_merge(
            array_keys($variables),
            ['Intersect', 'Intersect Strict', 'Except', 'Except Strict']
        ));

        $matrix = $this->matrix($variables);

        $fputcsvRecursion = function (array $matrix, $keys = []) use(&$fputcsvRecursion, $handle) {
            foreach ($matrix as $key => $current) {
                $mergeKeys = array_merge($keys, [$key]);

                is_multi_array($current)
                    ? $fputcsvRecursion($current, $mergeKeys)
                    : fputcsv($handle, array_merge($mergeKeys, $current));
            }
        };

        $fputcsvRecursion($matrix);

        fclose($handle);
        if (file_exists($filename)) {
            echo "CSV file created successfully: {$filename}".PHP_EOL;
        }
    }

    public function intersect(array $p, array $q)
    {
        return array_values(array_intersect($p, $q));
    }

    public function except(array $p, array $q)
    {
        return array_values(array_diff($p, $q));
    }
}
