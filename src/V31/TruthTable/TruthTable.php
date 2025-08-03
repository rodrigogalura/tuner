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

    public function matrix2d(array $variables)
    {
        if (count($variables) === 0) {
            return [];
        }

        if (count($variables) > 1) {
            $arr = [];

            $variable = array_shift($variables);

            foreach ($variable as $value) {
                $currentVariable = $this->matrix2d($variables);

                foreach ($currentVariable as $currentValue) {
                    $arr[] = array_merge([$value], is_array($currentValue) ? $currentValue : [$currentValue]);
                }
            }

            return $arr;
        }

        return array_shift($variables);
    }

    public function export($filename, array $variables)
    {
        $handle = fopen($filename, 'w');

        fputcsv($handle, ['Truth Table']);
        fputcsv($handle, array_merge(
            array_keys($variables),
            ['Intersect', 'Intersect Strict', 'Except', 'Except Strict']
        ));

        $matrix = $this->matrix2d($variables);

        foreach ($matrix as $m) {
            if (isset($current) && $current !== $m[0]) {
                fputcsv($handle, []);
            }

            $current = $m[0];
            fputcsv($handle, $m);
        }

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
