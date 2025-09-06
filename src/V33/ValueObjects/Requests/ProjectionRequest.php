<?php

namespace Laradigs\Tweaker\V33\ValueObjects\Requests;

use Exception;
use Laradigs\Tweaker\V33\Projection\ExceptProjection;
use Laradigs\Tweaker\V33\Projection\IntersectProjection;
use Laradigs\Tweaker\V33\Projection\Projectable;
use LogicException;

class ProjectionRequest extends Request
{
    private readonly string $projection;

    private static array $projections = [
        'intersect' => IntersectProjection::class,
        'except' => ExceptProjection::class,
    ];

    protected function validate()
    {
        $this->request = array_filter($this->request, fn ($paramKey): bool => in_array($paramKey, $this->key), ARRAY_FILTER_USE_KEY);

        switch (count($this->request)) {
            case 0:
                // noop
                break;

            case 1:
                $modifier = key($this->request);
                $projection = static::$projections[array_search($modifier, $this->key)];

                $this->setProjection($projection);

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
            throw new Exception('The class '.$projections.' is not implemenation of '.Projectable::class);
        }

        $this->projection = $projection;
    }

    public function getProjection(): ?string
    {
        return $this->projection ?? null;
    }

    public function __invoke(): array
    {
        return explode(',', current(parent::__invoke()));
    }
}
