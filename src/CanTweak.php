<?php

namespace Laradigs\Tweaker;

use Illuminate\Database\Eloquent\Builder;

// publish to config files
$tweakerConfig = [
    'projection' => [
        'include_key' => 'fields',
        'exclude_key' => 'fields!',
    ],
];

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
        $pc = $tweakerConfig['projection'];

        if (isset($pc['include_key']) xor isset($pc['exclude_key'])) {
            switch (true) {
                case isset($pc['include_key']):
                    $projection = new ProjectionField(
                        model: $this,
                        projectableFields: $this->getProjectableFields(),
                        definedFields: $builder->getQuery()->columns ?? ['*'],
                        clientInputFields: $_GET['fields'],
                    );

                    $builder->select($projection->project());

                    // code...
                    break;

                case isset($pc['exclude_key']):
                    // code...
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
