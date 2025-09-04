<?php

namespace Laradigs\Tweaker\V33;

use Illuminate\Database\Eloquent\Builder;

trait Tunable
{
    protected function getProjectableColumns(): array
    {
        return ['foo', 'id'];
    }

    /**
     * @return void
     */
    public function scopeSend(
        Builder $builder,
    ) {
        $visibleColumns = array_diff(
            $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable()),
            $this->getHidden()
        );

        return TunerBuilder::getInstance($builder, $visibleColumns, config('tweaker'), $_GET)
            ->projection($this->getProjectableColumns())
            ->execute();

        // try {
        //     return TunerBuilder::getInstance($builder, $visibleColumns, config('tweaker'), $_GET)
        //         ->projection($this->getProjectableColumns())
        //         ->execute();
        // } catch (Exception $e) {
        //     switch ($e->getCode()) {
        //         case ProjectionError::ProjectedColumnIsEmpty->errorCode():
        //             return [];

        //         default:
        //             throw $e;
        //     }
        // }
    }
}
