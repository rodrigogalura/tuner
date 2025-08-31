<?php

namespace Laradigs\Tweaker\V32;

use Illuminate\Database\Eloquent\Builder;
use Laradigs\Tweaker\V32\Projection\TunerProjection;
use Laradigs\Tweaker\V32\ValueObjects\ProjectionInput;

/**
 * Singleton
 */
final class TunerBuilder
{
    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    private function __construct(
        private Builder $builder,
        private array $visibleColumns,
        private array $config,
        private array $input
    ) {
        //
    }

    /**
     * Singletons should not be cloneable.
     */
    protected function __clone() {}

    /**
     * Singletons should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize a singleton.');
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

        TunerProjection::process(
            $this->builder,
            $this->visibleColumns,
            $projectableColumns,
            definedColumns: $this->builder->getQuery()->columns ?? ['*'],
            input: new ProjectionInput($config, $this->input)
        );

        return $this;
    }

    public function execute()
    {
        return $this->builder->get();
    }
}
