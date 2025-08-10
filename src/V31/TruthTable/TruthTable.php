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
        // set_time_limit(1);
        // dd($this->powerSet($allValues));
        //
    }

    public function powerSet()
    {
        $set = $this->allValues;

        $powerSet = [];
        $setNum = count($set);
        $setLastIndex = $setNum-1; // 4

        $pivots = [0];

        $loops = 20;

        while (($pivotsNum = count($pivots)) <= $setNum && ($loops-- > 0)) {
            $pc = $pivots; // pivots copy
            dump($pc);

            $start = array_pop($pc); // 4

            for ($i = $start; $i < $setNum; $i++) {
                $subSets = array_map(fn ($pivot) => $set[$pivot], $pc);
                $subSets[] = $set[$i];
                dump($subSets);

                $powerSet[] = implode(', ', $subSets);
            }

            $active = array_pop($pc);
            if (is_null($active)) {
                $pivots = range(0, $pivotsNum);
                continue;
            }

            $activePos = $start-1; // 3

            if ($activePos === $setLastIndex-1) {
                // dd($powerSet);
                // die('pahinga');
                $otherPivotsNum = 0;
                while (!is_null($op = array_pop($pc))) { // 1
                    dump($op);
                    dump($activePos-1);
                    if ($op === $activePos-1) {
                        $activePos = $op;
                        $otherPivotsNum++;
                    } else {
                        $pivots = $pc;

                        $pivots[$op] = $op+1; // 1 => 2

                        for ($j = 1; $j <= $otherPivotsNum+2; $j++) { // +2 for active pivot and counter
                            $pivots[$op+$j] = $op+1+$j;
                            // 2 => 3
                            // 3 => 4
                        }

                        // $pivots[$op+1] = $op+2; // active pivot
                        // $pivots[$op+2] = $op+3; // counter

                        $pivots = array_values($pivots);
                        continue 2;
                    }
                }

                $pivots = range(0, $pivotsNum);
                continue;

            } else {
                $pivots = $pc;
                $pivots[$activePos] = $activePos+1;
                $pivots[$start] = $pivots[$activePos]+1;

                $pivots = array_values($pivots);

                continue;
            }
        }

        array_unshift($powerSet, '');

        return $powerSet;






        // $setNum = count($set);

        // $powerSet = [];

        // $pivots = [];

        // while (($pivotsNum = count($pivots)) < $setNum) {

        //     // $subSets = [];
        //     for ($i = $pivotsNum; $i < $setNum; $i++) {
        //         $subSets = array_map(fn ($pivot) => $set[$pivot], $pivots);
        //         $subSets[] = $set[$i];

        //         $powerSet[] = implode(', ', $subSets);
        //     }

        //     $pivots = range(0, $pivotsNum);
        // }

        // return $powerSet;
    }

    // private function powerSets()
    // {
    //     $allValuesNum = count($this->allValues);

    //     // $powerSets = ['']; // act as null value in math
    //     $powerSets = [];

    //     // $pivotsLeft = [0,1,2];
    //     $pivotsLeft = [0];
    //     $pivotsRight = [];

    //     set_time_limit(2);

    //     do {
    //         $preservedPivotsNum = count($pivotsLeft);

    //         $i = array_pop($pivotsLeft);
    //         $pivotsRight = count($pivotsLeft) > 1 ? [array_pop($pivotsLeft)] : [];

    //         while (true) {
    //             while ($i < $allValuesNum) {
    //                 $left = array_map(fn ($pivotPos) => $this->allValues[$pivotPos], $pivotsLeft);
    //                 $right = array_map(fn ($pivotPos) => $this->allValues[$pivotPos], $pivotsRight);

    //                 $subSets = array_merge($left, $right);
    //                 $subSets[] = $this->allValues[$i];

    //                 array_push($powerSets, implode(', ', $subSets));

    //                 // dump('left: '    . implode(', ', $left));
    //                 // dump('right: '   . implode(', ', $right));
    //                 // dump('current: ' . $this->allValues[$i]);
    //                 // dump('           ----');

    //                 $i++;
    //             }

    //             if (count($pivotsRight) > 0) {
    //                 $lastIndex = array_key_last($pivotsRight);
    //                 $pivotRightPos = $pivotsRight[$lastIndex];

    //                 if ($pivotRightPos < ($allValuesNum-1)) {
    //                     $i = (++$pivotsRight[$lastIndex]) + 1;
    //                     continue;
    //                 }
    //             }

    //             // dump('ha?');
    //             if (count($pivotsLeft) > 0) {
    //                 $lastIndex = array_key_last($pivotsLeft);
    //                 $pivotLeftPosF1 = $pivotsLeft[$lastIndex]+1;

    //                 if ($pivotLeftPosF1 < ($allValuesNum-1)) {
    //                     $pivotsLeft[$lastIndex] = $pivotLeftPosF1;
    //                     $pivotsRight = range($pivotLeftPosF1+1, $pivotLeftPosF1+count($pivotsRight));
    //                     $i = $pivotLeftPosF1+2;

    //                     // $rightLastIndex = array_key_last($pivotsRight);
    //                     // $i = !is_null($rightLastIndex)
    //                     //     ? $rightLastIndex + 1
    //                     //     : $pivotLeftPos + 2;

    //                     // array_unshift($pivotsRight, array_pop($pivotsLeft));
    //                 } else {
    //                     break;
    //                 }
    //             } else {
    //                 break;
    //             }
    //         }

    //         $pivotsLeft = range(0, $preservedPivotsNum);

    //     } while(count($pivotsLeft) <= 4);

    //     dd($powerSets);
    // }

    // private function powerSets()
    // {
    //     $allValuesNum = count($this->allValues);

    //     // $powerSets = ['']; // act as null value in math
    //     $powerSets = [];

    //     $pivotsLeft = [0];

    //     do {
    //         $preservedPivotsNum = count($pivotsLeft);

    //         $i = array_pop($pivotsLeft);
    //         $pivotsRight = count($pivotsLeft) > 1 ? [array_pop($pivotsLeft)] : [];

    //         while (true) {
    //             while ($i < $allValuesNum) {
    //                 $left = array_map(fn ($pivotPos) => $this->allValues[$pivotPos], $pivotsLeft);
    //                 $right = array_map(fn ($pivotPos) => $this->allValues[$pivotPos], $pivotsRight);

    //                 $subSets = array_merge($left, $right);
    //                 $subSets[] = $this->allValues[$i];

    //                 array_push($powerSets, implode(', ', $subSets));

    //                 dump('left: '    . implode(', ', $left));
    //                 dump('right: '   . implode(', ', $right));
    //                 dump('current: ' . $this->allValues[$i]);
    //                 dump('           ----');

    //                 $i++;
    //             }

    //             $lastIndexRight = array_key_last($pivotsRight);
    //             if (count($pivotsRight) > 0) {
    //                 $pos = $pivotsRight[$lastIndexRight];

    //                 if ($pos < ($allValuesNum-1)) {
    //                     $i = (++$pivotsRight[$lastIndexRight]) + 1;
    //                     continue;
    //                 }
    //             }

    //             dump('ha?');
    //             if (count($pivotsLeft) > 0) {
    //                 $lastIndexLeft = array_key_last($pivotsLeft);
    //                 $posF1 = $pivotsLeft[$lastIndexLeft]+1;

    //                 if ($posF1 < ($allValuesNum-1)) {
    //                     array_pop($pivotsLeft);

    //                     $pivotsRight = range($posF1, $posF1+count($pivotsRight));

    //                     // $i = $posF1+count($pivotsRight)+1;

    //                     // array_unshift($pivotsRight, $posF1);


    //                     // $pivotsLeft[$lastIndex] = $posF1;
    //                     // $pivotsRight = range($posF1+1, $posF1+count($pivotsRight));
    //                     // $i = $posF1+2;

    //                     // $rightLastIndex = array_key_last($pivotsRight);
    //                     $i = !is_null($lastIndexRight)
    //                         ? $pos + 1
    //                         : count($pivotsRight)+1;

    //                     dump($pivotsLeft);
    //                     dump($pivotsRight);
    //                     dump($i);
    //                     // dump($pos ?? []);
    //                     // dump($posF1 ?? []);

    //                     // array_unshift($pivotsRight, array_pop($pivotsLeft));
    //                 } else {
    //                     break;
    //                 }
    //             } else {
    //                 break;
    //             }
    //         }

    //         $pivotsLeft = range(0, $preservedPivotsNum);

    //     } while(count($pivotsLeft) <= 4);

    //     dd($powerSets);
    // }

    // private function powerSets()
    // {
    //     $allValuesNum = count($this->allValues);

    //     // $powerSets = ['']; // act as null value in math
    //     $powerSets = [];

    //     $pivotsLeft = [0];

    //     do {
    //         $preservedPivotsNum = count($pivotsLeft);

    //         $i = array_pop($pivotsLeft);

    //         while (true) {
    //             while ($i < $allValuesNum) {
    //                 $subSets = array_merge(
    //                     array_map(fn ($pivotIndex) => $this->allValues[$pivotIndex], $pivotsLeft),
    //                     // array_map(fn ($pivotIndex) => $this->allValues[$pivotIndex], $pivotsRight)
    //                 );
    //                 $subSets[] = $this->allValues[$i];

    //                 array_push($powerSets, implode(', ', $subSets));

    //                 $i++;
    //             }

    //             if (($pivotsRemaining = count($pivotsLeft)) > 0) {
    //                 $pivotLeftIndex = ++$pivotsLeft[$pivotsRemaining-1];

    //                 if ($pivotLeftIndex < $allValuesNum) {
    //                     $i = $pivotLeftIndex+1;
    //                 } else {
    //                     break;
    //                 }
    //             } else {
    //                 break;
    //             }
    //         }

    //         $pivotsLeft = range(0, $preservedPivotsNum);

    //     } while(count($pivotsLeft) <= 2);

    //     dd($powerSets);
    // }

    // private function powerSets()
    // {
    //     $allValuesNum = count($this->allValues);

    //     // $powerSets = ['']; // act as null value in math
    //     $powerSets = [];

    //     $pivotsLeft = [0];

    //     do {
    //         $pivotsNum = count($pivotsLeft);

    //         do {
    //             $pivotsRight[]
    //                 = array_pop($pivotsLeft);

    //             $i // counter
    //                 = array_pop($pivotsRight);

    //             while ($i < $allValuesNum) {
    //                 $subSets = array_merge(
    //                     array_map(fn ($pivotIndex) => $this->allValues[$pivotIndex], $pivotsLeft),
    //                     array_map(fn ($pivotIndex) => $this->allValues[$pivotIndex], $pivotsRight)
    //                 );
    //                 $subSets[] = $this->allValues[$i];

    //                 array_push($powerSets, implode(', ', $subSets));

    //                 $i++;
    //             }

    //             if (($pivotLeftNum = count($pivotsLeft)) > 0) {
    //                 $pivotsLeft[array_key_last($pivotsLeft)]++;
    //             }
    //         } while ($pivotLeftNum > 0);

    //         $pivotsLeft = range(0, $pivotsNum); // [0,1]

    //         // $pivotsLeft = range(0, count($pivotsLeft) + 1); // +1 because the pivotsLeft was pop

    //         // die('finish');
    //     // } while($pivotsNum !== $allValuesNum);
    //     } while(count($pivotsLeft) <= 2);

    //     dd($powerSets);
    // }

    // private function powerSets()
    // {
    //     $allValuesNum = count($this->allValues);

    //     // $powerSets = ['']; // act as null value in math
    //     $powerSets = [];

    //     $pivots = [0];
    //     do {
    //         $i = array_pop($pivots); // counter

    //         while ($i < $allValuesNum) {
    //             $subSets = array_map(fn ($pivotIndex) => $this->allValues[$pivotIndex], $pivots);
    //             $subSets[] = $this->allValues[$i];

    //             array_push($powerSets, implode(', ', $subSets));

    //             $i++;
    //         }

    //         $pivots = range(0, count($pivots) + 1); // +1 because the pivots was pop





    //         $pivotsNum = count($pivots);

    //         // die('finish');
    //     // } while($pivotsNum !== $allValuesNum);
    //     } while($pivotsNum <= 2);

    //     dd($powerSets);
    // }

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
