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

        // $tweaker = new TweakerBuilder(
        //     builder: $builder,
        //     model: $this,
        //     config: config('tweaker'),
        //     clientInput: $_GET
        // );

        // return $tweaker
        //     ->projection($this->getProjectableFields())
        //     // ->filter()
        //     // ->inFiter()
        //     // ->betweenFilter()
        //     // ->searchFilter()
        //     // ->sort()
        //     // ->limit()
        //     // ->offset()
        //     // ->debug()
        //     // ->paginate()
        //     ->get();

        // $clientInputField = $_GET[$config['projection']['include_key']] ?? null;
        // $clientInputFieldNot = $_GET[$config['projection']['exclude_key']] ?? null;

        // if (isset($clientInputField) xor isset($clientInputFieldNot)) {
        //     $projection = match (true) {
        //         ! is_null($clientInputField) => new ProjectionField(
        //             model: $this,
        //             projectableFields: $this->getProjectableFields(),
        //             definedFields: $builder->getQuery()->columns ?? ['*'],
        //             clientInput: filter_explode($clientInputField),
        //         ),
        //         ! is_null($clientInputFieldNot) => new ProjectionFieldNot(
        //             model: $this,
        //             projectableFields: $this->getProjectableFields(),
        //             definedFields: $builder->getQuery()->columns ?? ['*'],
        //             clientInput: filter_explode($clientInputFieldNot),
        //         ),
        //     };

        //     try {
        //         if (empty($projectedFields = $projection->project())) {
        //             return [];
        //         }

        //         $builder->select($projectedFields);
        //     } catch (NoActionWillPerformException $e) {
        //         //
        //     }
        // }

        // $searchConfig = $config['searching'];
        // $clientInputSearch = $_GET[$searchConfig['key']] ?? null;

        // if (isset($clientInputSearch)) {
        //     $searching = new Searching(
        //         model: $this,
        //         searchableFields: $this->getSearchableFields(),
        //         clientInput: $clientInputSearch,
        //     );

        //     try {
        //         if (empty($searchResult = $searching->search())) {
        //             return [];
        //         }

        //         $builder->whereAny(filter_explode(key($searchResult)), 'LIKE', current($searchResult));
        //     } catch (NoActionWillPerformException $e) {
        //         //
        //     }

        // }

        // return $builder->get();
    }
}
