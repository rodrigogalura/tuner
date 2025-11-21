<?php

namespace Tuner\Requests;

use Illuminate\Support\Str;
use Tuner\Exceptions\ClientException;
use Tuner\Fields\Fields;
use Tuner\Fields\FilterableFields;
use Tuner\Tuner;

use function Tuner\explode_sanitize;

/**
 * @internal
 */
class FilterRequest extends Request implements RequestInterface
{
    const KEY_FILTER = 'filter';

    const KEY_IN = 'in';

    const KEY_BETWEEN = 'between';

    const LOGICAL_OPERATOR_AND = 'AND';

    const LOGICAL_OPERATOR_AND_NOT = 'AND!';

    const LOGICAL_OPERATOR_OR = 'OR';

    const LOGICAL_OPERATOR_OR_NOT = 'OR!';

    const COMPARISON_OPERATOR_EQ = '=';

    const COMPARISON_OPERATOR_G = '>';

    const COMPARISON_OPERATOR_L = '<';

    const COMPARISON_OPERATOR_GE = '<=';

    const COMPARISON_OPERATOR_LE = '>=';

    const COMPARISON_OPERATOR_NE = '<>';

    public function __construct(
        array $request,
        array $config,
        private array $visibleFields,
        private array $filterableFields,
    ) {
        parent::__construct($request, $config[Tuner::PARAM_KEY]);
    }

    private function validLogicalOperators()
    {
        return [
            static::LOGICAL_OPERATOR_AND,
            static::LOGICAL_OPERATOR_AND_NOT,
            static::LOGICAL_OPERATOR_OR,
            static::LOGICAL_OPERATOR_OR_NOT,
        ];
    }

    private function validateLogicalOperator($operator)
    {
        $validOperators = static::validLogicalOperators();
        throw_unless(in_array($operator, $validOperators), new ClientException("Invalid operator [{$operator}]. It must be one of these: [".implode(', ', $validOperators).']'));
    }

    private function logicFieldInterpreter(string $logicField)
    {
        $logicFieldArr = explode_sanitize($logicField, ' ');

        switch (count($logicFieldArr)) {
            case 0:
                return [];

            case 1:
                $field = $logicFieldArr[0];

                return [static::LOGICAL_OPERATOR_AND, $field, false];

            case 2:
                $this->validateLogicalOperator($bool = strtoupper($logicFieldArr[0]));
                $field = $logicFieldArr[1];

                if ($not = str_ends_with($bool, '!')) {
                    $bool = rtrim($bool, '!');
                }

                return [$bool, $field, $not];

            default:
                throw new ClientException("The [{$logicField}] is not valid logic field.");
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
                throw new ClientException('The ['.$key.'] is not a valid key!');
        }
    }

    private function getAllFieldsInlogicField(array $logicFields)
    {
        return array_map(function ($logicField) {
            switch (str_word_count($logicField)) {
                case 1:
                    return $logicField;

                case 2:
                    return Str::afterLast($logicField, ' ');

                default:
                    throw new ClientException("The [{$logicField}] is invalid.");
            }
        }, $logicFields);
    }

    protected function validate()
    {
        $filterableFields = (new FilterableFields($this->filterableFields, $this->visibleFields))();

        $interpretedRequest = [];

        foreach ($this->request as $key => $filterRequest) {
            // Validate filter
            throw_unless(is_array($filterRequest), new ClientException('The ['.$key.'] must be array'));

            $fields = $this->getAllFieldsInlogicField(array_keys($filterRequest));

            // Validate fields
            $fields = new Fields($fields, $filterableFields);
            throw_if(empty($fields->intersect()->get()), new ClientException('Invalid fields provided. It must be one of the following filterable fields: ['.implode(', ', $filterableFields).']'));

            foreach ($filterRequest as $logicField => $value) {
                $interpretedRequest[$key][] = array_merge(
                    $this->logicFieldInterpreter($logicField),
                    $this->valueInterpreter($key, $value)
                );
            }
        }

        $this->request = $interpretedRequest;
    }
}
