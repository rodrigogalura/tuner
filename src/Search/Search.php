<?php

namespace Laradigs\Tweaker\Search;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laradigs\Tweaker\Projection\NoActionWillPerformException;
use Laradigs\Tweaker\TruthTable;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;

use function RGalura\ApiIgniter\filter_explode;

class Search
{
    protected TruthTable $truthTable;

    public function __construct(
        private Model $model,
        protected array $searchableFields,
        private array $clientInput,
        private int $minimumLength = 2
    ) {
        $this->truthTable = new TruthTable(
            $model->getConnection()
                ->getSchemaBuilder()
                ->getColumnListing($model->getTable())
        );
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

    protected function validate(array $fields, string $keyword)
    {
        if (empty($fields)) {
            throw new NoActionWillPerformException;
        }

        if ($this->checkIfNotInVisibleFields($fields)) {
            throw new NoActionWillPerformException;
        }

        $sanitizeKeyword = trim(trim($keyword, '*'));

        if (empty($sanitizeKeyword) || strlen($sanitizeKeyword) < $this->minimumLength) {
            throw new NoActionWillPerformException;
        }

        if (empty($this->searchableFields)) {
            throw new NoActionWillPerformException;
        }

        $this->truthTable->extractIfAsterisk($this->searchableFields);
        $this->throwIfNotInVisibleFields($this->searchableFields);
    }

    public function search()
    {
        $fields = filter_explode(key($this->clientInput));
        $this->truthTable->extractIfAsterisk($fields);

        $this->validate($fields, $keyword = current($this->clientInput));

        if (! str_starts_with($keyword, '*') && ! str_ends_with($keyword, '*')) {
            $keyword = "*{$keyword}*";
        }

        // convert asterisk to percentage of first and last position of keyword
        $keyword = Str::replaceMatches('/^\*|\*$/', '%', $keyword);

        return [implode(', ', $this->truthTable->intersect($this->searchableFields, $fields)) => $keyword];
    }
}
