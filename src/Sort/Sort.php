<?php

namespace Laradigs\Tweaker\Sort;

use Illuminate\Database\Eloquent\Model;
use Laradigs\Tweaker\Projection\NoActionWillPerformException;
use Laradigs\Tweaker\TruthTable;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;

use function RGalura\ApiIgniter\filter_explode;

class Sort
{
    protected TruthTable $truthTable;

    public function __construct(
        private Model $model,
        protected array $sortableFields,
        private array $clientInput,
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

    protected function validate(array $fields, string $value)
    {
        if (
            empty($fields) ||
            $this->checkIfNotInVisibleFields($fields) ||
            empty($value) ||
            empty($this->sortableFields)
        ) {
            throw new NoActionWillPerformException;
        }

        $this->truthTable->extractIfAsterisk($this->sortableFields);
        $this->throwIfNotInVisibleFields($this->sortableFields);
    }

    public function search()
    {
        $fields = filter_explode(key($this->clientInput));
        $this->truthTable->extractIfAsterisk($fields);

        $this->validate($fields, $value = current($this->clientInput));

        // if (! str_starts_with($keyword, '*') && ! str_ends_with($keyword, '*')) {
        //     $keyword = "*{$keyword}*";
        // }

        // // convert asterisk to percentage of first and last position of keyword
        // $keyword = Str::replaceMatches('/^\*|\*$/', '%', $keyword);

        // return [implode(', ', $this->truthTable->intersect($this->sortableFields, $fields)) => $keyword];
    }
}
