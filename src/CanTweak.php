<?php

namespace Laradigs\Tweaker;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use function RGalura\ApiIgniter\http_response_error;
use Laradigs\Tweaker\Projection\Exceptions\CannotUseMultipleProjectionException;

trait CanTweak
{
    private readonly array $visibleFields;

    protected function getProjectableFields(): array
    {
        return ['*'];
    }

    protected function getSearchableFields(): array
    {
        return array_slice($this->visibleFields, 0, 2); // first two columns
    }

    protected function getSortableFields(): array
    {
        return ['id'];
    }

    /**
     * @return void
     */
    public function scopeSend(
        Builder $builder,
    ) {
        $truthTable = new \Laradigs\Tweaker\V31\TruthTable\TruthTable(
            // allValues: ['a', 'b', 'c', 'd']
            allValues: ['a', 'b', 'c', 'd', 'e']
        );

        dd($truthTable->powerSet());

        die;

        $this->visibleFields = array_diff(
            $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable()),
            $this->getHidden()
        );

        try {
            return TweakerBuilder::getInstance(
                $builder,
                $this->visibleFields,
                config('tweaker'),
                clientInput: $_GET
            )
                ->projection($this->getProjectableFields())
            // ->filter()
            // ->inFiter()
            // ->betweenFilter()
            // ->searchFilter($this->getSearchableFields())
                ->sort($this->getSortableFields())
            // ->limit()
            // ->offset()
            // ->debug()
            // ->paginate()
                ->execute();
        } catch (CannotUseMultipleProjectionException $e) {
            return response()->json(http_response_error($e->getMessage()), $e->getCode());
        } catch (ValidationException $e) {
            $error = current($e->errors());

            return response()->json([
                'success' => false,
                'message' => $error[0],
            ], $e->status);
        }
    }
}
