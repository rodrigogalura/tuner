<?php

namespace Laradigs\Tweaker\V32;

use Laradigs\Tweaker\V32\TunerBuilder;
use Illuminate\Database\Eloquent\Builder;
use Laradigs\Tweaker\V32\ValueObjects\TunerInput;

trait Tunable
{
    private readonly array $visibleColumns;

    protected function getProjectableColumns(): array
    {
        return ['*'];
    }

    /**
     * @return void
     */
    public function scopeSend(
        Builder $builder,
    ) {
        $this->visibleColumns = array_diff(
            $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable()),
            $this->getHidden()
        );

        return TunerBuilder::getInstance(
            $builder,
            $this->visibleColumns,
            config('tweaker'),
            TunerInput::sanitize($_GET)->get()
        )
        ->projection($this->getProjectableColumns())
        ->execute();
    }
}
