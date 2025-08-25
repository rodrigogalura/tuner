<?php

namespace Laradigs\Tweaker\V31\Projection;

use function RGalura\ApiIgniter\filter_explode;
use Laradigs\Tweaker\V31\TruthTable\Exportable;
use Laradigs\Tweaker\V31\TruthTable\TruthTable;

class ProjectionTruthTable extends TruthTable
{
    use Exportable;

    const PROJECTABLE_INDEX = 0;
    const DEFINED_INDEX = 1;
    const CLIENT_INPUT_INDEX = 2;
    const INTERSECT_NON_STRICT_INDEX = 3;
    const INTERSECT_STRICT_INDEX = 4;
    const EXCEPT_NON_STRICT_INDEX = 5;
    const EXCEPT_STRICT_INDEX = 6;

    private array $enables = [
        self::INTERSECT_NON_STRICT_INDEX => true,
        self::INTERSECT_STRICT_INDEX => false,
        self::EXCEPT_NON_STRICT_INDEX => false,
        self::EXCEPT_STRICT_INDEX => false,
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
        $this->enables[static::INTERSECT_NON_STRICT_INDEX] = $enable;
    }

    public function enableIntersectStrict($enable = true)
    {
        $this->enables[static::INTERSECT_STRICT_INDEX] = $enable;
    }

    public function enableExcept($enable = true)
    {
        $this->enables[static::EXCEPT_NON_STRICT_INDEX] = $enable;
    }

    public function enableExceptStrict($enable = true)
    {
        $this->enables[static::EXCEPT_STRICT_INDEX] = $enable;
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

                    // $truthTable[$i][static::INTERSECT_NON_STRICT_INDEX]
                    //     = $truthTable[$i][static::INTERSECT_STRICT_INDEX]
                    //     = $truthTable[$i][static::EXCEPT_NON_STRICT_INDEX]
                    //     = $truthTable[$i][static::EXCEPT_STRICT_INDEX]
                    //     = $code;

                    break;
                }
            }

            ksort($truthTable[$i]);

            if ($rulePassed) {
                $projectable = filter_explode($this->intersectAllKeys([
                    $matrixRow[static::PROJECTABLE_INDEX],
                    $matrixRow[static::DEFINED_INDEX],
                ]));
                $clientInput = filter_explode($matrixRow[static::CLIENT_INPUT_INDEX]);

                $some = $this->someFirstNotInSecond($clientInput, $projectable);

                $intersect = implode(', ', $this->intersect($projectable, $clientInput));
                $except = implode(', ', $this->except($projectable, $clientInput));

                if ($this->enables[static::INTERSECT_NON_STRICT_INDEX]) {
                    $truthTable[$i][static::INTERSECT_NON_STRICT_INDEX] = $intersect;
                }

                if ($this->enables[static::INTERSECT_STRICT_INDEX]) {
                    $truthTable[$i][static::INTERSECT_STRICT_INDEX] = $some ? 422 : $intersect;
                }

                if ($this->enables[static::EXCEPT_NON_STRICT_INDEX]) {
                    $truthTable[$i][static::EXCEPT_NON_STRICT_INDEX] = $except;
                }

                if ($this->enables[static::EXCEPT_STRICT_INDEX]) {
                    $truthTable[$i][static::EXCEPT_STRICT_INDEX] = $some ? 422 : $except;
                }
            }
        }

        return $truthTable;
    }
}
