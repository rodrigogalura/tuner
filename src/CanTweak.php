<?php

namespace Laradigs\Tweaker;

use Illuminate\Database\Eloquent\Builder;

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
