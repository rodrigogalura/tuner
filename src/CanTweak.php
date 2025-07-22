<?php

namespace Laradigs\Tweaker;

use Illuminate\Database\Eloquent\Builder;

trait CanTweak
{
    private readonly array $columnListing;

    protected function getProjectableFields(): array
    {
        return ['*'];
    }

    protected function getSearchableFields(): array
    {
        return array_slice($this->columnListing, 0, 2); // first two columns
    }

    protected function getSortableFields(): array
    {
        return ['*'];
    }

    /**
     * @return void
     */
    public function scopeSend(
        Builder $builder,
    ) {
        $this->columnListing = array_diff(
            $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable()),
            $this->getHidden()
        );

        return TweakerBuilder::getInstance(
            $builder,
            $this->columnListing,
            config('tweaker'),
            clientInput: $_GET
        )
            ->projection($this->getProjectableFields())
        // ->filter()
        // ->inFiter()
        // ->betweenFilter()
            ->searchFilter($this->getSearchableFields())
            // ->sort($this->getSortableFields())
        // ->limit()
        // ->offset()
        // ->debug()
        // ->paginate()
            ->execute();
    }
}
