<?php

namespace Laradigs\Tweaker;

use Laradigs\Tweaker\TweakerBuilder;
use Illuminate\Database\Eloquent\Builder;
use Laradigs\Tweaker\Searching\Searching;
use function RGalura\ApiIgniter\filter_explode;
use Laradigs\Tweaker\Projection\ProjectionField;
use Laradigs\Tweaker\Projection\ProjectionFieldNot;
use Laradigs\Tweaker\Projection\NoActionWillPerformException;

trait CanTweak
{
    protected function getProjectableFields(): array
    {
        return ['*'];
    }

    protected function getSearchableFields(): array
    {
        return ['*'];
    }

    /**
     * @return void
     */
    public function scopeSend(
        Builder $builder,
    ) {
        return TweakerBuilder::getInstance(
            builder: $builder,
            model: $this,
            config: config('tweaker'),
            clientInput: $_GET
        )
        ->projection($this->getProjectableFields())
        // ->filter()
        // ->inFiter()
        // ->betweenFilter()
        ->searchFilter($this->getSearchableFields())
        // ->sort()
        // ->limit()
        // ->offset()
        // ->debug()
        // ->paginate()
        ->execute();
    }
}
