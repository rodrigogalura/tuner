<?php

namespace RodrigoGalura\Tuner\V33\ValueObjects\Requests;

use Exception;
use LogicException;
use RodrigoGalura\Tuner\V33\Tuner;
use RodrigoGalura\Tuner\V33\ValueObjects\Columns;
use RodrigoGalura\Tuner\V33\ValueObjects\DefinedColumns;
use RodrigoGalura\Tuner\V33\ValueObjects\ProjectableColumns;

class ProjectionRequest extends Request implements RequestInterface
{
    public function __construct(
        array $config,
        array $request,
        private array $visibleColumns,
        private array $projectableColumns,
        private array $definedColumns,
    ) {
        parent::__construct($config[Tuner::PARAM_KEY], $request);
    }

    protected function validate()
    {
        switch (count($this->request)) {
            case 1:
                $p = new ProjectableColumns($this->projectableColumns, $this->visibleColumns);
                $q = new DefinedColumns($this->definedColumns, $this->visibleColumns);
                $projectableColumns = array_intersect($p(), $q());

                // Validate projection
                [$paramKey, $paramValue] = [key($this->request), current($this->request)];
                throw_unless(is_string($paramValue), new Exception('The ['.$paramKey.'] must be string'));

                $projector = array_search($paramKey, $this->key);

                // Validate columns
                $columns = new Columns(explode(', ', $paramValue), $projectableColumns);
                throw_if(empty($projectedColumns = $columns->{$projector}()->get()), new Exception('The ['.$paramKey.'] must be use any of these projectable columns: ['.implode(', ', $projectableColumns).']'));

                $this->request = [$paramKey => $projectedColumns];

                break;

            case 2:
                $projectionModifiers = array_keys($this->request);
                throw new Exception('Cannot use ['.implode(', ', $projectionModifiers).'] at the same time.');
            default:
                throw new LogicException('Number of projection key is invalid.');
        }
    }
}
