<?php

namespace RodrigoGalura\Tuner\V33\ValueObjects\Requests;

use Exception;
use Illuminate\Support\Str;
use RodrigoGalura\Tuner\V33\Tuner;
use RodrigoGalura\Tuner\V33\ValueObjects\Columns;
use RodrigoGalura\Tuner\V33\ValueObjects\FilterableColumns;

use function RGalura\ApiIgniter\explode_sanitize;

class FilterRequest extends Request
{
    const KEY_FILTER = 'filter';

    const KEY_IN = 'in';

    const KEY_BETWEEN = 'between';

    const LOGICAL_OPERATOR_AND = 'AND';

    const LOGICAL_OPERATOR_OR = 'OR';

    const COMPARISON_OPERATOR_EQ = '=';

    const COMPARISON_OPERATOR_G = '>';

    const COMPARISON_OPERATOR_L = '<';

    const COMPARISON_OPERATOR_GE = '<=';

    const COMPARISON_OPERATOR_LE = '>=';

    const COMPARISON_OPERATOR_NE = '<>';

    public function __construct(
        array $config,
        array $request,
        private array $visibleColumns,
        private array $filterableColumns,
    ) {
        parent::__construct($config[Tuner::PARAM_KEY], $request);
    }

    private function validLogicalOperators()
    {
        return [
            static::LOGICAL_OPERATOR_AND,
            static::LOGICAL_OPERATOR_OR,
        ];
    }

    private function validateLogicalOperator($operator)
    {
        $validOperators = static::validLogicalOperators();
        throw_unless(in_array($operator, $validOperators), new Exception("Invalid operator [{$operator}]. It must be one of these: [".implode(', ', $validOperators).']'));
    }

    private function logicColumnInterpreter(string $logicColumn)
    {
        $logicColumnArr = explode_sanitize($logicColumn, ' ');

        switch (count($logicColumnArr)) {
            case 0:
                return [];

            case 1:
                $column = $logicColumnArr[0];

                return [static::LOGICAL_OPERATOR_AND, $column, false];

            case 2:
                $this->validateLogicalOperator($bool = strtoupper($logicColumnArr[0]));
                $column = $logicColumnArr[1];

                when($not = str_ends_with($bool, '!'), fn (): string => $bool = rtrim($bool, '!'));

                return [$bool, $column, $not];

            default:
                throw new Exception("The [{$logicColumn}] is not valid logic column.");
        }
    }

    private function valueInterpreter(string $key, string $value)
    {
        switch ($key) {
            case static::KEY_FILTER:
                switch (true) {
                    case empty($value):
                        return [static::COMPARISON_OPERATOR_EQ, ''];

                    case in_array($op = substr($value, 0, 2), [
                        static::COMPARISON_OPERATOR_GE,
                        static::COMPARISON_OPERATOR_LE,
                        static::COMPARISON_OPERATOR_NE,
                    ]):
                        return [$op, trim(substr($value, 2))];

                    case in_array($op = $value[0], [
                        static::COMPARISON_OPERATOR_EQ,
                        static::COMPARISON_OPERATOR_G,
                        static::COMPARISON_OPERATOR_L,
                    ]):
                        return [$op, trim(substr($value, 1))];

                    default:
                        return [static::COMPARISON_OPERATOR_EQ, trim($value)];
                }

            case static::KEY_IN:
            case static::KEY_BETWEEN:
                return [explode_sanitize($value)];

            default:
                throw new Exception('The ['.$key.'] is not a valid key!');
        }
    }

    private function getAllColumnsInlogicColumn(array $logicColumns)
    {
        return array_map(function ($logicColumn) {
            switch (str_word_count($logicColumn)) {
                case 1:
                    return $logicColumn;

                case 2:
                    return Str::afterLast($logicColumn, ' ');

                default:
                    throw new Exception("The [{$logicColumn}] is invalid.");
            }
        }, $logicColumns);
    }

    protected function validate()
    {
        $filterableColumns = (new FilterableColumns($this->filterableColumns, $this->visibleColumns))();

        $interpretedRequest = [];

        foreach ($this->request as $key => $filterRequest) {
            // Validate filter
            throw_unless(is_array($filterRequest), new Exception('The ['.$key.'] must be array'));

            $columns = $this->getAllColumnsInlogicColumn(array_keys($filterRequest));

            // Validate columns
            $columns = new Columns($columns, $filterableColumns);
            throw_if(empty($columns->intersect()->get()), new Exception('Invalid columns provided. It must be one of the following filterable columns: '.implode(', ', $filterableColumns)));

            foreach ($filterRequest as $logicColumn => $value) {
                $interpretedRequest[$key][] = array_merge(
                    $this->logicColumnInterpreter($logicColumn),
                    $this->valueInterpreter($key, $value)
                );
            }
        }

        $this->request = $interpretedRequest;
    }
}
