<?php

namespace Laradigs\Tweaker;

use Illuminate\Database\Eloquent\Builder;
use Laradigs\Tweaker\Projection\NoActionWillPerformException;
use Laradigs\Tweaker\Projection\ProjectionField;
use Laradigs\Tweaker\Projection\ProjectionFieldNot;

use function RGalura\ApiIgniter\filter_explode;

trait CanTweak
{
    protected function getProjectableFields(): array
    {
        return ['*'];
    }

    /**
     * @return void
     */
    public function scopeSend(
        Builder $builder,
    ) {
        $projectionConfig = config('tweaker.projection');

        $clientInputField = $_GET[$projectionConfig['include_key']] ?? null;
        $clientInputFieldNot = $_GET[$projectionConfig['exclude_key']] ?? null;

        if (isset($clientInputField) xor isset($clientInputFieldNot)) {
            $projection = match (true) {
                ! is_null($clientInputField) => new ProjectionField(
                    model: $this,
                    projectableFields: $this->getProjectableFields(),
                    definedFields: $builder->getQuery()->columns ?? ['*'],
                    clientInput: filter_explode($clientInputField),
                ),
                ! is_null($clientInputFieldNot) => new ProjectionFieldNot(
                    model: $this,
                    projectableFields: $this->getProjectableFields(),
                    definedFields: $builder->getQuery()->columns ?? ['*'],
                    clientInput: filter_explode($clientInputFieldNot),
                ),
            };

            try {
                if (empty($projectedFields = $projection->project())) {
                    return [];
                }

                $builder->select($projectedFields);
            } catch (NoActionWillPerformException $e) {
                //
            }
        }

        return $builder->get();
    }
}
