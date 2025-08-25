<?php

namespace Laradigs\Tweaker\V31;

class PowerSet
{
    public function __construct(private array $set)
    {
        //
    }

    public function handle()
    {
        $powerSet = [];

        $SET_COUNT = count($this->set);
        $SET_LAST_INDEX = $SET_COUNT-1;

        $pivots = [0];
        while (count($pivots) <= $SET_COUNT) {
            $pc = $pivots; # pivots copy

            $start = array_pop($pc);
            for ($i = $start; $i < $SET_COUNT; $i++) {
                $subSets = array_map(fn ($pivot) => $this->set[$pivot], $pc);
                $subSets[] = $this->set[$i];

                $powerSet[] = implode(', ', $subSets);
            }

            $prevPivotsNum = 0;
            while (!is_null($op = array_pop($pc))) {
                $isActivePivotAtTheEndPos = $op === ($SET_LAST_INDEX-1-($prevPivotsNum));

                if ($isActivePivotAtTheEndPos) {
                    $activePos = $op;
                    $prevPivotsNum++;
                } else {
                    $pivots = $pc; # reset pivots
                    $pivots[$op] = $op+1;

                    for ($j = 1; $j <= $prevPivotsNum+1; $j++) { # +1 for counter
                        $pivots[$op+$j] = $op+$j+1;
                    }

                    $pivots = array_values($pivots);
                    continue 2;
                }
            }

            # Add 1 pivot
            $pivots = range(0, count($pivots));
        }

        array_unshift($powerSet, '');

        return $powerSet;
    }
}
