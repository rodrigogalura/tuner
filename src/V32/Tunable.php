<?php

namespace Laradigs\Tweaker\V32;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Laradigs\Tweaker\V32\Projection\ErrorEnum as ProjectionError;

trait Tunable
{
    private readonly array $visibleColumns;

    protected function getProjectableColumns(): array
    {
        return ['id', 'name'];
    }

    /**
     * @return void
     */
    public function scopeSend(
        Builder $builder,
    ) {
        // // $p = new IntersectProjection([1,2,3], [2, 3]);
        // $p = new ExceptProjection([1,2,3], [2, 3]);

        // $projector = new Projector($p);

        // dd($projector());

        $this->visibleColumns = array_diff(
            $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable()),
            $this->getHidden()
        );

        try {
            // return TunerBuilder::getInstance(
            //     $builder,
            //     $this->visibleColumns,
            //     config('tweaker'),
            //     TunerInput::sanitize($_GET)->get()
            // )
            //     ->projection($this->getProjectableColumns())
            //     ->execute();
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case ProjectionError::ProjectedColumnIsEmpty->errorCode():
                    return [];

                default:
                    throw $e;
            }
        }
    }
}
