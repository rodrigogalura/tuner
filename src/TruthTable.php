<?php

namespace Laradigs\Tweaker;

use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;

class TruthTable
{
    public function __construct(
        private array $allItems
    )
    {
        //
    }

    protected function extractIfAsterisk(&$var)
    {
        if ($var === ['*']) {
            $var = $this->allItems;
        }
    }

    protected function diffFromAllItems(array $fields)
    {
        return $this->diff($fields, $this->allItems);
    }

    public function intersect(array $arr1, array $arr2)
    {
        return array_values(array_intersect($arr1, $arr2));
    }

    public function diff(array $arr1, array $arr2)
    {
        return array_values(array_diff($arr1, $arr2));
    }
}
