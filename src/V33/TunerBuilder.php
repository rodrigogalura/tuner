<?php

namespace Laradigs\Tweaker\V33;

use function RGalura\ApiIgniter\some;
use Laradigs\Tweaker\V32\HasSingleton;
use function RGalura\ApiIgniter\every;
use Illuminate\Database\Eloquent\Builder;
use Laradigs\Tweaker\V33\Projection\Projector;
use Laradigs\Tweaker\V33\ValueObjects\ArrayParser;
use Laradigs\Tweaker\V32\Projection\ErrorEnum as Error;
use Laradigs\Tweaker\V33\Projection\IntersectProjection;

final class TunerBuilder
{
    use HasSingleton;

    private ?array $projectedColumns = null;

    private readonly array $definedColumns;
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
        $this->definedColumns = $builder->getQuery()->columns ?? ['*'];
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

        /**
         * Check if the projection is used
         */
        if (some($this->queryKeywords, $projectionKeywords)) {
            /**
             * Check if the projection's varieties are used more than 1
             */
            if (count($projectionKeywords) > 1) {
                # All options are used
                throw_if(every($this->queryKeywords, $projectionKeywords), new \LogicException('Cannot use '.implode(', ', $projectionKeywords).' at the same time.'));
            }

            $validateProjectableColumns = function(&$columns) {
                throw_if(empty($columns), Error::P_Disabled->exception());

                $columns = (new ArrayParser($columns))
                    ->assignIfEqTo(['*'], $this->visibleColumns)
                    ->sanitize()
                    ->get();

                throw_unless(some($columns, $this->visibleColumns), Error::P_NotInColumns->exception(invalidColumns: $columns));

                $columns = IntersectProjection::from($this->visibleColumns)
                    ->to($columns)
                    ->project();
            };

            try {
                $validateProjectableColumns($projectableColumns);
            } catch (\Exception $e) {
                switch ($e->getCode()) {
                    case Error::P_Disabled->getCode():
                        logger()->info('Skip the projection process');
                        goto end;
                        break;

                    default:
                        throw $e;
                }
            }

            foreach ($projectionConfig as $key => $keyword) {
                if (in_array($keyword, $this->queryKeywords)) {
                    $class = __NAMESPACE__.'\\Projection\\'.
                                str($key)
                                    ->before('_keyword')
                                    ->title()
                                    ->value.'Projection';

                    $inputValue = (new ArrayParser(explode(',', $_GET[$keyword])))
                        ->assignIfEqTo(['*'], $this->visibleColumns)
                        ->sanitize()
                        ->get();

                    $projector = new Projector(
                        new $class($projectableColumns, $inputValue)
                    );

                    $this->projectedColumns = $projector();
                }
            }
        }

        end:
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
