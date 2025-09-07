<?php

namespace RodrigoGalura\Tuner\V33\ValueObjects\Requests;

use Exception;
use LogicException;
use RodrigoGalura\Tuner\V33\Projection\ExceptProjection;
use RodrigoGalura\Tuner\V33\Projection\IntersectProjection;
use RodrigoGalura\Tuner\V33\Projection\Projectable;
use RodrigoGalura\Tuner\V33\ValueObjects\Columns;

class ProjectionRequest extends MultipleKeysRequest
{
    private readonly string $projection;

    private static array $projections = [
        'intersect' => IntersectProjection::class,
        'except' => ExceptProjection::class,
    ];

    protected function validate()
    {
        switch (count($this->request)) {
            case 1:
                $paramKey = key($this->request);

                $this->setProjection(
                    static::$projections[array_search($paramKey, $this->multipleKeys)]
                );

                $paramValue = current($this->request);

                throw_unless(is_string($paramValue), new Exception('The '.$paramKey.' must be string'));

                $column = new Columns(explode(', ', $paramValue), $this->visibleColumns);

                throw_if(empty($this->request = $column()), new Exception('The '.$paramKey.' must be use any of these valid columns: '.implode(', ', $this->visibleColumns)));

                break;

            case 2:
                $projectionModifiers = array_keys($this->request);
                throw new Exception('Cannot use '.implode(', ', $projectionModifiers).' at the same time.');
            default:
                throw new LogicException('Number of projection key is invalid.');
        }
    }

    /**
     * Set the projection property only if the param $projection implements the Projectable contract
     */
    private function setProjection(string $projection): void
    {
        if (! is_subclass_of($projection, Projectable::class)) {
            throw new Exception('The class ['.$projections.'] is not implemenation of '.Projectable::class);
        }

        $this->projection = $projection;
    }

    public function getProjection(): ?string
    {
        return $this->projection ?? null;
    }
}
