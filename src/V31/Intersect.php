<?php

namespace Laradigs\Tweaker\V31;

class Intersect
{
    public function __invoke(array $arr1, array $arr2)
    {
        return array_values(array_intersect($arr1, $arr2));
    }
}
