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

    private array $enabled = [
        self::INDEX_INTERSECT_NON_STRICT => true,
        self::INDEX_INTERSECT_STRICT => false,
        self::INDEX_EXCEPT_NON_STRICT => false,
        self::INDEX_EXCEPT_STRICT => false,
    ];

    public function __construct(
        private array $rules = [],
        array $items = []
    ) {
        parent::__construct($items);
    }

    private function validate(array $rules, $subjects, $subject)
    {
        foreach ($rules as $rule) {
            switch (true) {
                case is_object($rule):
                    if ($rule->failed($subject)) {
                        return $rule->getErrorCode();
                    }
                    break;

                case is_array($rule):
                    $this->extractIfAsterisk($subjects[$rule['targetArgsIndex']]);
                    $arg = filter_explode($subjects[$rule['targetArgsIndex']]);

                    $ruleClass = new $rule['classRule']($arg, $rule['errorEnum']);
                    if ($ruleClass->failed($subject)) {
                        return $ruleClass->getErrorCode();
                    }
                    break;
            }
        }

        return parent::RULE_PASSED_CODE;
    }

    public function enabledIntersect($enable = true)
    {
        $this->enabled[static::INDEX_INTERSECT_NON_STRICT] = $enable;
    }

    public function enabledIntersectStrict($enable = true)
    {
        $this->enabled[static::INDEX_INTERSECT_STRICT] = $enable;
    }

    public function enabledExcept($enable = true)
    {
        $this->enabled[static::INDEX_EXCEPT_NON_STRICT] = $enable;
    }

    public function enabledExceptStrict($enable = true)
    {
        $this->enabled[static::INDEX_EXCEPT_STRICT] = $enable;
    }

    public function enabledAll($enable = true)
    {
        $indexes = array_keys($this->enabled);

        while ($index = current($indexes)) {
            $this->enabled[$index] = $enable;

            next($indexes);
        }
    }

    /**
     * @return mixed[]
     */
    public function truthTable(array $matrix2D): array
    {
        $truthTable = [];

        foreach ($matrix2D as $i => $matrixRow) {
            $truthTable[$i] = $matrixRow;

            $rulePassed = true;
            foreach ($this->rules as $index => $rules) {
                $this->extractIfAsterisk($matrixRow[$index]);

                $code = $this->validate($rules, $matrixRow, $matrixRow[$index]);

                if ($code !== parent::RULE_PASSED_CODE) {
                    $rulePassed = false;

                    foreach ($this->enabled as $index => $enabled) {
                        if ($enabled) {
                            $truthTable[$i][$index] = $code;
                        }
                    }

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

                if ($this->enabled[static::INDEX_INTERSECT_NON_STRICT]) {
                    $truthTable[$i][static::INDEX_INTERSECT_NON_STRICT] = $intersect;
                }

                if ($this->enabled[static::INDEX_INTERSECT_STRICT]) {
                    $truthTable[$i][static::INDEX_INTERSECT_STRICT] = $some ? 422 : $intersect;
                }

                if ($this->enabled[static::INDEX_EXCEPT_NON_STRICT]) {
                    $truthTable[$i][static::INDEX_EXCEPT_NON_STRICT] = $except;
                }

                if ($this->enabled[static::INDEX_EXCEPT_STRICT]) {
                    $truthTable[$i][static::INDEX_EXCEPT_STRICT] = $some ? 422 : $except;
                }
            }
        }

        return $truthTable;
    }
}
