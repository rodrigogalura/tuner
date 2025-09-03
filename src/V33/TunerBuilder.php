<?php

namespace Laradigs\Tweaker\V33;

use Illuminate\Database\Eloquent\Builder;
use Laradigs\Tweaker\V32\HasSingleton;
use Laradigs\Tweaker\V33\Projection\Projector;
use Laradigs\Tweaker\V33\ValueObjects\ArrayParser;

use function RGalura\ApiIgniter\in_array_all;

final class TunerBuilder
{
    use HasSingleton;

    private ?array $projectedColumns = null;

    private readonly array $queryKeywords;

    /**
     * Private constructor
     */
    private function __construct(
        private Builder $builder,
        private array $visibleColumns,
        private array $config,
        private array $query
    ) {
        $this->queryKeywords = array_keys($query);
    }

    private function config(string $modifier)
    {
        return $this->config[str($modifier)->after('::')->value];
    }

    public static function getInstance()
    {
        return new self(...func_get_args());
    }

    public function projection(array $projectableColumns)
    {
        $projectionConfig = $this->config(__METHOD__);
        $projectionKeywords = array_values($projectionConfig);

        if (count($projectionKeywords) > 1) {
            // All options are used
            if (in_array_all($this->queryKeywords, $projectionKeywords)) {
                throw new \LogicException('Cannot use '.implode(', ', $projectionConfig).' at the same time.');
            }
        }

        foreach ($projectionConfig as $key => $keyword) {
            if (in_array($keyword, $this->queryKeywords)) {
                $class = __NAMESPACE__.'\\Projection\\'.
                            str($key)
                                ->before('_keyword')
                                ->title()
                                ->value.'Projection';

                $projectableColumns = (new ArrayParser($projectableColumns))
                    ->assignIfEq(['*'], $this->visibleColumns)
                    ->sanitize()
                    ->get();

                $inputValue = (new ArrayParser(explode(',', $_GET[$keyword])))
                    ->assignIfEq(['*'], $this->visibleColumns)
                    ->sanitize()
                    ->get();

                $projector = new Projector(
                    new $class($projectableColumns, $inputValue)
                );

                $this->projectedColumns = $projector();
            }
        }

        return $this;
    }

    public function execute()
    {
        if (empty($this->projectedColumns)) {
            return [];
        }

        $this->builder->select($this->projectedColumns);

        return $this->builder->get();
    }
}
