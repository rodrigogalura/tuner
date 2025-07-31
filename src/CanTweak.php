<?php

namespace Laradigs\Tweaker;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Laradigs\Tweaker\Projection\Exceptions\CannotUseMultipleProjectionException;

use function RGalura\ApiIgniter\http_response_error;

class NotOnListRules {
    public function __construct(private array $list, private $errorCode = 1)
    {
        //
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function handle($item)
    {
        return !in_array($item, $this->list);
    }
}

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
        $projectableColumns = ['id', 'name'];
        $definedColumns = ['id', 'name'];
        $clientInput = ['id', 'name'];

        // $projectableColumns = ['a', 'b', 'c'];
        // $definedColumns = ['d', 'e', 'f'];

        $truthTable = new \Laradigs\Tweaker\V31\TruthTable([
            0 => # projectable
            [
                new NotOnListRules(['id', 'name'])
            ],

            1 => [ // defined

            ]
        ]);

        // dd($truthTable->matrix([$projectableColumns, $definedColumns, $clientInput]));

        $variables = [
            $projectableColumns,
            $definedColumns,
            $clientInput // client input must be last on variables
        ];

        dd($truthTable->matrix($variables));

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
