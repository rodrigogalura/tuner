<?php

namespace Laradigs\Tweaker\TruthTableGenerator;

abstract class TruthTableGenerator
{
    protected const VISIBLE_COLUMNS = ['id', 'name'];

    protected const PLACEHOLDER_EMPTY = '[EMPTY]';

    protected const PLACEHOLDER_UNPROCESSABLE = 422;

    protected $handle;

    public function __construct(protected $filename)
    {
        $this->handle = fopen($filename, 'w');
    }

    protected function skipRow()
    {
        fputcsv($this->handle, []);
    }

    abstract public function generate();
}
