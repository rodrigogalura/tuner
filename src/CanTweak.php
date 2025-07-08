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
        $projection = new Projection($this, $_GET);
        $projection->setSelectFields($this->getProjectableFields());

        dd($projection->getSelectFields());
    }
}
