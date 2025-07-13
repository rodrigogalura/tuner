<?php

namespace Laradigs\Tweaker;

use Illuminate\Database\Eloquent\Builder;
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
            switch (true) {
                case !is_null($clientInputField):
                    $projection = new ProjectionField(
                        model: $this,
                        projectableFields: $this->getProjectableFields(),
                        definedFields: $builder->getQuery()->columns ?? ['*'],
                        clientInput: filter_explode($clientInputField),
                    );

                    $builder->select($projection->project());

                    // code...
                    break;

                case !is_null($clientInputFieldNot):
                    $projection = new ProjectionFieldNot(
                        model: $this,
                        projectableFields: $this->getProjectableFields(),
                        definedFields: $builder->getQuery()->columns ?? ['*'],
                        clientInput: filter_explode($clientInputFieldNot),
                    );

                    $builder->select($projection->project());

                    break;
            }

            // $projection = new Projection(
            //     model: $this,
            //     projectableFields: $this->getProjectableFields(),
            //     definedFields: $builder->getQuery()->columns ?? ['*'],
            //     clientInput: [
            //         'fields' => $_GET['fields'] ?? null,
            //         'fields!' => $_GET['fields!'] ?? null,
            //     ],
            // );

            // if (! is_null($projectedFields = $projection->handle()?->getProjectedFields())) {
            //     $builder->select($projectedFields);
            // }
        }

        return $builder->get();
    }
}
