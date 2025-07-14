<?php

namespace Laradigs\Tweaker\Projection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;

abstract class Searching
{
    public function __construct(
        private Model $model,
        protected array $searchableFields,
        private array $clientInput,
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
        if (! empty($diff = array_diff($fields, $this->visibleFields()))) {
            throw new InvalidFieldsException($diff, 1);
        }
    }

    protected function validate($fields, $keyword)
    {
        $fields = key($this->clientInput);

        if (empty($fields)) {
            throw new NoActionWillPerformException;
        }

        $keyword = current($this->clientInput);
        $sanitizeKeyword = trim(trim($keyword, '*'));

        if (empty($sanitizeKeyword) || strlen($sanitizeKeyword) < config('tweaker.searching.minimum_length')) {
            throw new NoActionWillPerformException;
        }

        if (empty($this->searchableFields())) {
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
        $fields = key($this->clientInput);
        $keyword = current($this->clientInput);

        $this->validate($fields, $keyword);

        // convert asterisk to percentage of first and last position of keyword
        $keyword = Str::replaceMatches('/^\*|\*$/', '%', $keyword);

        return [implode(', ', array_intersect($this->searchableFields, $fields)) => $keyword];
    }
}
