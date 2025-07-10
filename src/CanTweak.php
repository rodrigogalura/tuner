<?php

namespace Laradigs\Tweaker;

use Illuminate\Database\Eloquent\Builder;

trait CanTweak
{
    private function getProjectableFields(): array
    {
        return ['*'];
    }

    /**
     * @return void
     */
    public function scopeSend(
        Builder $builder,
    ) {
        $projection = new Projection(
            model: $this,
            projectableFields: $this->getProjectableFields(),
            definedFields: $builder->getQuery()->columns ?? ['*'],
            clientInput: $_GET,
        );

        if (! is_null($projectedFields = $projection->handle()?->getProjectedFields())) {
            $builder->select($projectedFields);
        }

        return $builder->get();
    }
}
