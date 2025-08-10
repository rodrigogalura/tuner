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
        private array $allValues = []
    )
    {
        set_time_limit(1);
    }

    public function powerSet()
    {
        $SET = $this->allValues;

        $powerSet = [];
        $SET_COUNT = count($SET);
        $SET_LAST_INDEX = $SET_COUNT-1;

        $addPivot = function(&$pivots) {
            $pivots = range(0, count($pivots));
        };

        $pivots = [0];
        while (count($pivots) <= $SET_COUNT) {
            $pc = $pivots; # pivots copy

            $start = array_pop($pc);
            for ($i = $start; $i < $SET_COUNT; $i++) {
                $subSets = array_map(fn ($pivot) => $SET[$pivot], $pc);
                $subSets[] = $SET[$i];

                $powerSet[] = implode(', ', $subSets);
            }

            $active = array_pop($pc);
            if (is_null($active)) {
                $addPivot($pivots);
                continue;
            }

            $activePos = $start-1;

            $isActivePivotInSecondToTheLast = $activePos === $SET_LAST_INDEX-1;

            if ($isActivePivotInSecondToTheLast) {

                $otherPivotsNum = 0;
                while (!is_null($op = array_pop($pc))) {
                    $isOtherPivotInPreviousActivePivot = $op === $activePos-1;

                    if ($isOtherPivotInPreviousActivePivot) {
                        $activePos = $op;
                        $otherPivotsNum++;
                    } else {
                        $pivots = $pc; # reset pivots
                        $pivots[$op] = $op+1;

                        for ($j = 1; $j <= $otherPivotsNum+2; $j++) { # +2 for active pivot and counter
                            $pivots[$op+$j] = $op+1+$j;
                        }

                        $pivots = array_values($pivots);
                        continue 2;
                    }
                }

                $addPivot($pivots);
            } else {
                $pivots = $pc; # reset pivots
                $pivots[$activePos] = $activePos+1;
                $pivots[$start] = $pivots[$activePos]+1;

                $pivots = array_values($pivots);
            }
        }

        array_unshift($powerSet, '');

        return $powerSet;
    }

    private function extractIfAsterisk(string &$str)
    {
        if (trim($str) === '*') {
            $str = implode(', ', $this->allValues);
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

    private function someFirstNotInSecond(array $first, array $second)
    {
        return ! empty (array_diff($first, $second));
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

        $PROJECTABLE_INDEX = 0;
        $DEFINED_INDEX = 1;
        $CLIENT_INPUT_INDEX = 2;
        $INTERSECT_NON_STRICT_INDEX = 3;
        $INTERSECT_STRICT_INDEX = 4;
        $EXCEPT_NON_STRICT_INDEX = 5;
        $EXCEPT_STRICT_INDEX = 6;

        foreach ($matrix2d as $i => $matrixRow) {
            $matrixProjection[$i] = $matrixRow;

            $rulePassed = true;
            foreach ($this->rules as $index => $rules) {
                $this->extractIfAsterisk($matrixRow[$index]);

                $code = $this->validate($rules, $matrixRow[$index]);

                if ($code !== static::RULE_PASSED_CODE) {
                    $rulePassed = false;

                    $matrixProjection[$i][$INTERSECT_NON_STRICT_INDEX]
                        = $matrixProjection[$i][$INTERSECT_STRICT_INDEX]
                        = $matrixProjection[$i][$EXCEPT_NON_STRICT_INDEX]
                        = $matrixProjection[$i][$EXCEPT_STRICT_INDEX]
                        = $code;

                    break;
                }
            }

            ksort($matrixProjection[$i]);

            if ($rulePassed) {
                $projectable = filter_explode($this->intersectAllKeys([
                    $matrixRow[$PROJECTABLE_INDEX],
                    $matrixRow[$DEFINED_INDEX],
                ]));
                $clientInput = filter_explode($matrixRow[$CLIENT_INPUT_INDEX]);

                $some = $this->someFirstNotInSecond($clientInput, $projectable);

                $intersect = implode(', ', $this->intersect($projectable, $clientInput));
                $except = implode(', ', $this->except($projectable, $clientInput));

                $matrixProjection[$i][$INTERSECT_NON_STRICT_INDEX] = $intersect;
                $matrixProjection[$i][$INTERSECT_STRICT_INDEX] = $some ? 422 : $intersect;

                $matrixProjection[$i][$EXCEPT_NON_STRICT_INDEX] = $except;
                $matrixProjection[$i][$EXCEPT_STRICT_INDEX] = $some ? 422 : $except;
            }
        }

        return $matrixProjection;
    }
}
