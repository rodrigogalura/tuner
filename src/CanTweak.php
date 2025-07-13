<?php

namespace Laradigs\Tweaker;

use Illuminate\Database\Eloquent\Builder;
use Laradigs\Tweaker\Projection\Projection;
use function RGalura\ApiIgniter\filter_explode;
use Laradigs\Tweaker\Projection\ProjectionField;
use Laradigs\Tweaker\Projection\ProjectionFieldNot;

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
        // publish to config files
        $tweakerConfig = [
            'projection' => [
                'include_key' => 'fields',
                'exclude_key' => 'fields!',
            ],
        ];

        $pc = $tweakerConfig['projection'];

        if (
            $clientInputField = $_GET[$pc['include_key']] ?? null xor
            $clientInputFieldNot = $_GET[$pc['exclude_key']] ?? null
        ) {
            $projection = match(true) {
                !is_null($clientInputField) => new ProjectionField(
                    model: $this,
                    projectableFields: $this->getProjectableFields(),
                    definedFields: $builder->getQuery()->columns ?? ['*'],
                    clientInput: filter_explode($clientInputField),
                ),
                !is_null($clientInputFieldNot) => new ProjectionFieldNot(
                    model: $this,
                    projectableFields: $this->getProjectableFields(),
                    definedFields: $builder->getQuery()->columns ?? ['*'],
                    clientInput: filter_explode($clientInputFieldNot),
                ),
            };

            if (empty($projectedFields = $projection->project())) {
                return Projection::EMPTY_VALUE;
            }

            $builder->select($projectedFields);
        }

        return $builder->get();
    }
}
