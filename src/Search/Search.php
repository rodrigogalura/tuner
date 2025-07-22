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
        array $searchConfig = ['key' => 'search'],
    ) {
        $this->truthTable = new TruthTable(
            $model->getConnection()
                ->getSchemaBuilder()
                ->getColumnListing($model->getTable())
        );

        $this->clientInput = Arr::get($this->clientInput, $searchConfig['key']);
        $this->prerequisites();
    }

    private function prerequisites()
    {
        $searchIsNotUsed = is_null($this->clientInput);
        $searchIsNotALinearArray = !is_array($this->clientInput) || is_multi_array($this->clientInput);

        throw_if($searchIsNotUsed || $searchIsNotALinearArray, NoActionWillPerformException::class);
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
        $this->fields = filter_explode(key($this->clientInput));
        throw_if(empty($this->fields), NoActionWillPerformException::class);

        $this->truthTable->extractIfAsterisk($this->fields);
        throw_if($this->checkIfNotInVisibleFields($this->fields), NoActionWillPerformException::class);

        $this->keyword = current($this->clientInput);
        $sanitizeKeyword = trim(trim($this->keyword, '*'));

        throw_if(empty($sanitizeKeyword), NoActionWillPerformException::class);

        $belowTheRequiredLength = strlen($sanitizeKeyword) < $this->minimumLength;
        $searchableFieldsAreEmpty = empty($this->searchableFields);

        throw_if($belowTheRequiredLength || $searchableFieldsAreEmpty, NoActionWillPerformException::class);

        $this->truthTable->extractIfAsterisk($this->searchableFields);
        $this->throwIfNotInVisibleFields($this->searchableFields);
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
