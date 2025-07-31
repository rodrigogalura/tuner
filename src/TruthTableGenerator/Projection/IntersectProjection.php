<?php

namespace Laradigs\Tweaker\TruthTableGenerator\Projection;

class IntersectProjection extends Projection
{
    private function nonStrictIntersect(array $p, array $q, array $r)
    {
        return array_intersect(array_intersect($p, $q), $r);
    }

    private function strictIntersect(array $nonStrictIntersect, array $r)
    {
        return $this->someArr1NotInArr2(arr1: $r, arr2: $nonStrictIntersect)
                ? [parent::PLACEHOLDER_UNPROCESSABLE]
                : $nonStrictIntersect;
    }
}
