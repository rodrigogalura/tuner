<?php

namespace Tuner\Requests;

use Tuner\Exceptions\ClientException;
use Tuner\Fields\Fields;
use Tuner\Fields\SortableFields;
use Tuner\Tuner;

/**
 * @internal
 */
class SortRequest extends Request implements RequestInterface
{
    private const ORDERS = [
        'asc' => ['a', 'asc', 'ascending'],
        'desc' => ['-', 'd', 'des', 'desc', 'descending'],
    ];

    public function __construct(
        array $request,
        array $config,
        private array $visibleFields,
        private array $sortableFields,
    ) {
        parent::__construct($request, $config[Tuner::PARAM_KEY]);
    }

    private static function validOrderValues()
    {
        return array_merge(static::ORDERS['asc'], static::ORDERS['desc']);
    }

    private static function orderInterpreter($request)
    {
        foreach ($request as $field => $order) {
            $filtered = array_filter(static::ORDERS, fn ($values): bool => in_array($order, $values), ARRAY_FILTER_USE_BOTH);
            $request[$field] = key($filtered);
        }

        return $request;
    }

    protected function validate()
    {
        $sortableFields = (new SortableFields($this->sortableFields, $this->visibleFields))();

        // Validate sort
        $sortRequest = current($this->request); // unwrap
        throw_unless(is_array($sortRequest), new ClientException('The ['.$this->key.'] must be array'));

        // Validate fields
        $fields = new Fields(array_keys($sortRequest), $sortableFields);
        throw_if(empty($requestedFields = $fields->intersect()->get()), new ClientException('Invalid fields provided. It must be one of the following sortable fields: ['.implode(', ', $sortableFields).']'));

        $filteredRequest = array_filter($sortRequest, fn ($field): bool => in_array($field, $requestedFields), ARRAY_FILTER_USE_KEY);

        // Validate values
        $validOrderValues = static::validOrderValues();
        $filteredRequest = array_filter($filteredRequest, fn ($order): bool => in_array($order, $validOrderValues));

        throw_if(empty($filteredRequest), new ClientException('The ['.$this->key.'] must be use any of these valid order: ['.implode(', ', $validOrderValues).']'));

        $this->request = [$this->key => static::orderInterpreter($filteredRequest)];
    }
}
