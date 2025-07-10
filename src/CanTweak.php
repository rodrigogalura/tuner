<?php

namespace Laradigs\Tweaker;

use Laradigs\Tweaker\Projectable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

trait CanTweak
{
    private function getProjectableFields(): array
    {
        return ['*'];
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function scopeSend(
        Builder $builder,
    )
    {
        $projection = new Projection(
            model: $this,
            projectableFields: $this->getProjectableFields(),
            definedFields: $builder->getQuery()->columns ?? ['*'],
            clientInput: $_GET,
        );

        if (!empty($projectedFields = $projection->handle()?->getProjectedFields())) {
            $builder->select($projectedFields);
        }

        return $builder->get();

        // dd('not perform');
    }
}
