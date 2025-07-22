<?php

namespace Laradigs\Tweaker\Search;

use Illuminate\Support\Str;
use Laradigs\Tweaker\Projection\NoActionWillPerformException;
use Laradigs\Tweaker\TruthTable;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;

use function RGalura\ApiIgniter\filter_explode;
use function RGalura\ApiIgniter\is_multi_array;

class Search
{
    private array $fields;

    private string $keyword;

    protected TruthTable $truthTable;

    public function __construct(
        private array $columnListing,
        protected array $searchableFields,
        private array $clientInput,
        private int $minimumLength = 2,
    ) {
        $this->truthTable = new TruthTable($columnListing);

        $this->clientInput = current($clientInput);
    }

    private function prerequisites()
    {
        $searchIsNotALinearArray = is_multi_array($this->clientInput);
        throw_if(empty($this->clientInput) || $searchIsNotALinearArray, NoActionWillPerformException::class);
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
        $this->prerequisites();

        $this->fields = filter_explode(key($this->clientInput));
        $this->keyword = current($this->clientInput);

        throw_if(empty($this->fields), NoActionWillPerformException::class);

        $this->truthTable->extractIfAsterisk($this->fields);
        throw_if($this->checkIfNotInVisibleFields($this->fields), NoActionWillPerformException::class);

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
