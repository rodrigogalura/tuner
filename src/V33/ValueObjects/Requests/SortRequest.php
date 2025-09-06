<?php

namespace Laradigs\Tweaker\V33\ValueObjects\Requests;

use Exception;

class SortRequest extends SingleKeyRequest
{
    const VALID_VALUES = [
        '+', 'a', 'asc', 'ascending',
        '-', 'd', 'des', 'desc', 'descending',
    ];

    protected function validate()
    {
        $value = current($this->request);
        dd($value);

        throw_unless(is_array($value), new Exception('The '.$this->key.' must be array'));

        throw_unless(in_array($value, static::VALID_VALUES), new Exception('The '.$this->key.' must be use any of these valid order: '.implode(', ', static::VALID_VALUES)));
    }
}
