<?php

namespace Laradigs\Tweaker\Search;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laradigs\Tweaker\TruthTable;
use Illuminate\Database\Eloquent\Model;
use function RGalura\ApiIgniter\filter_explode;
use function RGalura\ApiIgniter\is_multi_array;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use Laradigs\Tweaker\Projection\NoActionWillPerformException;

class Search
{
    private array $fields;
    private string $keyword;

    protected TruthTable $truthTable;

    public function __construct(
        private Model $model,
        protected array $searchableFields,
        private mixed $clientInput,
        private int $minimumLength = 2,
        private array $searchConfig = ['key' => 'search'],
    ) {
        $this->truthTable = new TruthTable(
            $model->getConnection()
                ->getSchemaBuilder()
                ->getColumnListing($model->getTable())
        );

        $this->clientInput = Arr::get($this->clientInput, $this->searchConfig['key']);
    }

    private function throwIfNotInVisibleFields(array $fields)
    {
        if (! empty($diff = $this->truthTable->diffFromAllItems($fields))) {
            throw new InvalidFieldsException($diff, 1);
        }
    }

    private function checkIfNotInVisibleFields(array $fields)
    {
        return ! empty($this->truthTable->diffFromAllItems($fields));
    }

    protected function validate()
    {
        if (
            !is_array($this->clientInput) ||
            is_multi_array($this->clientInput)
        ) {
            throw new NoActionWillPerformException;
        }

        $this->fields = filter_explode(key($this->clientInput));

        if (empty($this->fields)) {
            throw new NoActionWillPerformException;
        }

        $this->truthTable->extractIfAsterisk($this->fields);

        if ($this->checkIfNotInVisibleFields($this->fields)) {
            throw new NoActionWillPerformException;
        }

        $this->keyword = current($this->clientInput);
        $sanitizeKeyword = trim(trim($this->keyword, '*'));

        if (empty($sanitizeKeyword) || strlen($sanitizeKeyword) < $this->minimumLength) {
            throw new NoActionWillPerformException;
        }

        if (empty($this->searchableFields)) {
            throw new NoActionWillPerformException;
        }

        $this->truthTable->extractIfAsterisk($this->searchableFields);
        $this->throwIfNotInVisibleFields($this->searchableFields);
    }

    public function isUsed()
    {
        return !is_null($this->clientInput);
    }

    public function search()
    {
        $this->validate();

        if (! str_starts_with($this->keyword, '*') && ! str_ends_with($this->keyword, '*')) {
            $this->keyword = "*{$this->keyword}*";
        }

        // convert asterisk to percentage of first and last position of this->keyword
        $this->keyword = Str::replaceMatches('/^\*|\*$/', '%', $this->keyword);

        return [implode(', ', $this->truthTable->intersect($this->searchableFields, $this->fields)) => $this->keyword];
    }
}
