<?php

namespace RodrigoGalura\Tuner\V33\ValueObjects\Requests;

use Exception;
use LogicException;
use RodrigoGalura\Tuner\V33\ValueObjects\Columns;
use RodrigoGalura\Tuner\V33\ValueObjects\DefinedColumns;
use RodrigoGalura\Tuner\V33\ValueObjects\ProjectableColumns;

class ProjectionRequest extends Request
{
    public function __construct(
        array $multipleKeys,
        array $request,
        private array $visibleColumns,
        private array $projectableColumns,
        private array $definedColumns,
    ) {
        parent::__construct($multipleKeys, $request);
    }

    protected function beforeValidate()
    {
        $this->request = array_filter($this->request, fn ($paramKey): bool => in_array($paramKey, $this->key), ARRAY_FILTER_USE_KEY);
    }

    protected function validate()
    {
        switch (count($this->request)) {
            case 1:
                $p = new ProjectableColumns($this->projectableColumns, $this->visibleColumns);
                $q = new DefinedColumns($this->definedColumns, $this->visibleColumns);
                $projectableColumns = array_intersect($p(), $q());

                $paramKey = key($this->request);
                $operator = array_search($paramKey, $this->key);

                $paramValue = current($this->request);
                throw_unless(is_string($paramValue), new Exception('The '.$paramKey.' must be string'));

                $columns = new Columns(explode(', ', $paramValue), $projectableColumns);
                throw_if(empty($this->request = $columns->{$operator}()->get()), new Exception('The '.$paramKey.' must be use any of these projectable columns: '.implode(', ', $projectableColumns)));

                break;

            case 2:
                $projectionModifiers = array_keys($this->request);
                throw new Exception('Cannot use '.implode(', ', $projectionModifiers).' at the same time.');
            default:
                throw new LogicException('Number of projection key is invalid.');
        }
    }
}
