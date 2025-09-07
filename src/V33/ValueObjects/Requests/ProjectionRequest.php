<?php

namespace RodrigoGalura\Tuner\V33\ValueObjects\Requests;

use Exception;
use LogicException;
use RodrigoGalura\Tuner\V33\ValueObjects\Columns;
use RodrigoGalura\Tuner\V33\ValueObjects\DefinedColumns;
use RodrigoGalura\Tuner\V33\ValueObjects\ProjectableColumns;

class ProjectionRequest extends MultipleKeysColumnRequest
{
    public function __construct(
        array $multipleKeys,
        array $visibleColumns,
        array $projectableColumns,
        array $definedColumns,
        array $request
    ) {
        $p = new ProjectableColumns($projectableColumns, $visibleColumns);
        $q = new DefinedColumns($definedColumns, $visibleColumns);

        parent::__construct($multipleKeys, array_intersect($p(), $q()), $request);
    }

    protected function validate()
    {
        switch (count($this->request)) {
            case 1:
                $paramKey = key($this->request);
                $operator = array_search($paramKey, $this->multipleKeys);

                $paramValue = current($this->request);
                throw_unless(is_string($paramValue), new Exception('The '.$paramKey.' must be string'));

                $columns = new Columns(explode(', ', $paramValue), $this->validColumns);
                throw_if(empty($this->request = $columns->{$operator}()->get()), new Exception('The '.$paramKey.' must be use any of these valid columns: '.implode(', ', $this->validColumns)));

                break;

            case 2:
                $projectionModifiers = array_keys($this->request);
                throw new Exception('Cannot use '.implode(', ', $projectionModifiers).' at the same time.');
            default:
                throw new LogicException('Number of projection key is invalid.');
        }
    }
}
