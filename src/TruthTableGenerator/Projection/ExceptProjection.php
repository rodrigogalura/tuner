<?php

namespace Laradigs\Tweaker\TruthTableGenerator\Projection;

class ExceptProjection extends Projection
{
    private function nonStrictExcept(array $p, array $q, array $r)
    {
        // return array_intersect(array_intersect($p, $q), $r);
    }

    private function strictExcept(array $nonStrictExcept, array $r)
    {
        // return $this->someArr1NotInArr2(arr1: $r, arr2: $nonStrictExcept)
        //         ? [parent::PLACEHOLDER_UNPROCESSABLE]
        //         : $nonStrictExcept;
    }
}
