<?php

namespace Laradigs\Tweaker\TruthTableGenerator;

use Laradigs\Tweaker\TruthTable;

abstract class TruthTableGenerator extends TruthTable
{
    protected const VISIBLE_COLUMNS = ['id', 'name'];

    protected const PLACEHOLDER_EMPTY = '[EMPTY]';

    protected const PLACEHOLDER_UNPROCESSABLE = 422;

    protected $handle;

    public function __construct(protected $filename)
    {
        $this->handle = fopen($filename, 'w');

        parent::__construct(static::VISIBLE_COLUMNS);
    }

    protected function skipRow()
    {
        fputcsv($this->handle, []);
    }

    abstract public function generate();
}
