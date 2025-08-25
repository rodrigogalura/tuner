<?php

namespace Laradigs\Tweaker\V31\Projection;

use Laradigs\Tweaker\V31\TruthTable\Exportable;
use Laradigs\Tweaker\V31\TruthTable\TruthTable;
use function RGalura\ApiIgniter\filter_explode;

class ProjectionTruthTable extends TruthTable
{
    use Exportable;

    const INDEX_PROJECTABLE = 0;
    const INDEX_DEFINED = 1;
    const INDEX_CLIENT_INPUT = 2;
    const INDEX_INTERSECT_NON_STRICT = 3;
    const INDEX_INTERSECT_STRICT = 4;
    const INDEX_EXCEPT_NON_STRICT = 5;
    const INDEX_EXCEPT_STRICT = 6;

    private array $enables = [
        self::INDEX_INTERSECT_NON_STRICT => true,
        self::INDEX_INTERSECT_STRICT => false,
        self::INDEX_EXCEPT_NON_STRICT => false,
        self::INDEX_EXCEPT_STRICT => false,
    ];

    public function __construct(
        private array $rules = [],
        array $items = []
    )
    {
        parent::__construct($items);
    }

    private function validate(array $rules, $item)
    {
        foreach ($rules as $rule) {
            if ($rule->handle($item)) {
                return $rule->getErrorCode();
            }
        }

        return parent::RULE_PASSED_CODE;
    }

    public function enableIntersect($enable = true)
    {
        $this->enables[static::INDEX_INTERSECT_NON_STRICT] = $enable;
    }

    public function enableIntersectStrict($enable = true)
    {
        $this->enables[static::INDEX_INTERSECT_STRICT] = $enable;
    }

    public function enableExcept($enable = true)
    {
        $this->enables[static::INDEX_EXCEPT_NON_STRICT] = $enable;
    }

    public function enableExceptStrict($enable = true)
    {
        $this->enables[static::INDEX_EXCEPT_STRICT] = $enable;
    }

    public function enableAll($enable = true)
    {
        $indexes = array_keys($this->enables);

        while ($index = current($indexes)) {
            $this->enables[$index] = $enable;

            next($indexes);
        }
    }

    public function truthTable(array $matrix2D)
    {
        $truthTable = [];

        foreach ($matrix2D as $i => $matrixRow) {
            $truthTable[$i] = $matrixRow;

            $rulePassed = true;
            foreach ($this->rules as $index => $rules) {
                $this->extractIfAsterisk($matrixRow[$index]);

                $code = $this->validate($rules, $matrixRow[$index]);

                if ($code !== parent::RULE_PASSED_CODE) {
                    $rulePassed = false;

                    foreach ($this->enables as $index => $enable) {
                        if ($enable) {
                            $truthTable[$i][$index] = $code;
                        }
                    }

                    // $truthTable[$i][static::INDEX_INTERSECT_NON_STRICT]
                    //     = $truthTable[$i][static::INDEX_INTERSECT_STRICT]
                    //     = $truthTable[$i][static::INDEX_EXCEPT_NON_STRICT]
                    //     = $truthTable[$i][static::INDEX_EXCEPT_STRICT]
                    //     = $code;

                    break;
                }
            }

            ksort($truthTable[$i]);

            if ($rulePassed) {
                $projectable = filter_explode($this->intersectAllKeys([
                    $matrixRow[static::INDEX_PROJECTABLE],
                    $matrixRow[static::INDEX_DEFINED],
                ]));
                $clientInput = filter_explode($matrixRow[static::INDEX_CLIENT_INPUT]);

                $some = $this->someFirstNotInSecond($clientInput, $projectable);

                $intersect = implode(', ', $this->intersect($projectable, $clientInput));
                $except = implode(', ', $this->except($projectable, $clientInput));

                if ($this->enables[static::INDEX_INTERSECT_NON_STRICT]) {
                    $truthTable[$i][static::INDEX_INTERSECT_NON_STRICT] = $intersect;
                }

                if ($this->enables[static::INDEX_INTERSECT_STRICT]) {
                    $truthTable[$i][static::INDEX_INTERSECT_STRICT] = $some ? 422 : $intersect;
                }

                if ($this->enables[static::INDEX_EXCEPT_NON_STRICT]) {
                    $truthTable[$i][static::INDEX_EXCEPT_NON_STRICT] = $except;
                }

                if ($this->enables[static::INDEX_EXCEPT_STRICT]) {
                    $truthTable[$i][static::INDEX_EXCEPT_STRICT] = $some ? 422 : $except;
                }
            }
        }

        return $truthTable;
    }
}
