<?php

namespace Laradigs\Tweaker\Searching;

use Illuminate\Support\Str;
use function RGalura\ApiIgniter\filter_explode;
use Illuminate\Database\Eloquent\Model;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use Laradigs\Tweaker\Projection\NoActionWillPerformException;

class Searching
{
    public function __construct(
        private Model $model,
        protected array $searchableFields,
        private array $clientInput,
        private int $minimumLength = 2
    ) {
        //
    }

    private function extractIfAsterisk(&$var)
    {
        if ($var === ['*']) {
            $var = $this->visibleFields();
        }
    }

    private function throwIfNotInVisibleFields(array $fields)
    {
        if ($this->checkIfNotInVisibleFields($fields)) {
            throw new InvalidFieldsException($diff, 1);
        }
    }

    private function checkIfNotInVisibleFields(array $fields)
    {
        return ! empty($diff = array_diff($fields, $this->visibleFields()));
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

        $this->extractIfAsterisk($this->searchableFields);
        $this->throwIfNotInVisibleFields($this->searchableFields);
    }

    public function visibleFields()
    {
        return $this->model->getConnection()->getSchemaBuilder()->getColumnListing($this->model->getTable());
    }

    public function search()
    {
        $fields = filter_explode(key($this->clientInput));
        $this->extractIfAsterisk($fields);

        $keyword = current($this->clientInput);

        $this->validate($fields, $keyword);

        if (! str_starts_with($keyword, '*') && ! str_ends_with($keyword, '*')) {
            $keyword = "*{$keyword}*";
        }

        // convert asterisk to percentage of first and last position of keyword
        $keyword = Str::replaceMatches('/^\*|\*$/', '%', $keyword);

        return [implode(', ', array_intersect($this->searchableFields, $fields)) => $keyword];
    }
}
