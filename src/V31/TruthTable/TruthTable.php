<?php

namespace Laradigs\Tweaker\V31\TruthTable;

use Laradigs\Tweaker\V31\TruthTable\Exportable;
use function RGalura\ApiIgniter\filter_explode;

class TruthTable
{
    use Exportable;

    private const RULE_PASSED_CODE = true;

    public function __construct(
        private array $rules = [],
        private array $asteriskValues = []
    )
    {
        //
    }

    private function extractIfAsterisk(string &$str)
    {
        if (trim($str) === '*') {
            $str = implode(', ', $this->asteriskValues);
        }
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

    public function intersect(array $p, array $q)
    {
        return array_values(array_intersect($p, $q));
    }

    public function except(array $p, array $q)
    {
        return array_values(array_diff($p, $q));
    }

    // private function someFirstNotInSecond(array $first, array $second)
    // {
    //     return ! empty (array_diff($first, $second));
    // }

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

    private function validate(array $rules, $value)
    {
        foreach ($rules as $rule) {
            if ($rule->handle($value)) {
                return $rule->getErrorCode();
            }
        }

        return static::RULE_PASSED_CODE;
    }

    public function matrixProjection(array $matrix2d)
    {
        $matrixProjection = [];

        $INTERSECT_NON_STRICT_INDEX = 3;
        $INTERSECT_STRICT_INDEX = 4;
        $EXCEPT_NON_STRICT_INDEX = 5;
        $EXCEPT_STRICT_INDEX = 6;

        foreach ($matrix2d as $i => $matrixRow) {
            $matrixProjection[$i] = $matrixRow;

            foreach ($this->rules as $index => $rules) {
                $value = $matrixRow[$index];
                $this->extractIfAsterisk($value);

                $code = $this->validate($rules, $value);

                if ($code !== static::RULE_PASSED_CODE) {
                    $matrixProjection[$i][$INTERSECT_NON_STRICT_INDEX]
                        = $matrixProjection[$i][$INTERSECT_STRICT_INDEX]
                        = $matrixProjection[$i][$EXCEPT_NON_STRICT_INDEX]
                        = $matrixProjection[$i][$EXCEPT_STRICT_INDEX]
                        = $code;

                    break;
                }
            }

            ksort($matrixProjection[$i]);
        }

        return $matrixProjection;
    }

    // public function export($filepath, $data)
    // {
    //     $handle = fopen($filepath, 'w');

    //     fputcsv($handle, ['Truth Table']);
    //     fputcsv($handle, [
    //         'Projectable (p)', 'Defined (q)', 'Client (r)',
    //         'Intersect - Non-strict',
    //         'Intersect - Strict',
    //         'Except - Non-strict',
    //         'Except - Strict',
    //     ]);

    //     foreach ($data as $d) {
    //         fputcsv($handle, $d);
    //     }

    //     fclose($handle);

    //     if (file_exists($filepath)) {
    //         echo "CSV file created successfully: {$filepath}".PHP_EOL;
    //     }
    // }
}
