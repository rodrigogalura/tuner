<?php

namespace Laradigs\Tweaker\V32;

use Illuminate\Database\Eloquent\Builder;
use Laradigs\Tweaker\V32\Projection\Projector;
use Laradigs\Tweaker\V32\ValueObjects\ProjectionInput;

final class TunerBuilder
{
    use HasSingleton;

    /**
     * Private constructor
     */
    private function __construct(
        private Builder $builder,
        private array $visibleColumns,
        private array $config,
        private array $input
    ) {
        //
    }

    public static function getInstance(
        Builder $builder,
        array $visibleColumns,
        array $config,
        array $input
    ) {
        return new self($builder, $visibleColumns, $config, $input);
    }

    public function projection(array $projectableColumns)
    {
        $config = $this->config[str(__METHOD__)->after('::')->value];

        Projector::run(
            $this->builder,
            $this->visibleColumns,
            $projectableColumns,
            definedColumns: $this->builder->getQuery()->columns ?? ['*'],
            input: new ProjectionInput($config, $this->input),
            strict: $config['strict'] ?? false
        );

        return $this;
    }

    public function execute()
    {
        return $this->builder->get();
    }
}
