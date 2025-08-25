<?php

namespace Laradigs\Tweaker\V31;

class Matrix2D
{
    public function __construct(private array $variables)
    {
        //
    }

    public function handle()
    {
        return $this->matrix2D($this->variables);
    }

    private function matrix2D(array $variables)
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
}
