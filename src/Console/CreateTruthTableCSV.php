<?php

namespace Laradigs\Tweaker\Console;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Laradigs\Tweaker\V31\Matrix2D;
use Laradigs\Tweaker\V31\PowerSet;
use Laradigs\Tweaker\V31\ErrorCodes;
use function Laravel\Prompts\select;
use function RGalura\ApiIgniter\base_path;
use Laradigs\Tweaker\V31\TruthTable\Rules\FalsyRule;
use Laradigs\Tweaker\V31\Projection\DefinedErrorCodes;
use Laradigs\Tweaker\TruthTableGenerator\ProjectionCSV;
use Laradigs\Tweaker\V31\TruthTable\Rules\NotOnListRule;
use Laradigs\Tweaker\V31\Projection\ProjectionTruthTable;
use Laradigs\Tweaker\V31\Projection\ProjectableErrorCodes;

class CreateTruthTableCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'create:truth-table {option?} {--copy}';
    protected $signature = 'create:truth-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Truth Table CSV File';

    const TRUTH_TABLE_PROJECTION = 'Projection';

    # all columns of table except hidden columns
    const VISIBLE_COLUMNS = ['id', 'name'];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        switch (select(
            label: 'Select the Truth Table do you want to generate CSV:',
            options: array_map('class_basename', [
                static::TRUTH_TABLE_PROJECTION,
                "Foo",
                "Bar"
            ])
        )) {
            case static::TRUTH_TABLE_PROJECTION:
                $this->exportProjection();
                break;
        }
    }

    private function exportProjection()
    {
        $PROJECTABLE_COLUMNS_HEADER = 'Projectable Columns (p)';
        $CSV_HEADERS = [$PROJECTABLE_COLUMNS_HEADER, 'Defined Columns (q)', 'Client Input (r)'];

        $variables = array_fill_keys(
            $CSV_HEADERS, // Client Input variable must be at last index
            array_merge(['*'], (new PowerSet(static::VISIBLE_COLUMNS))->handle())
        );

        $class = ProjectionTruthTable::class;

        $ptt = new $class(
            rules:
            [
                # Client Input Rules
                2 => [
                    new FalsyRule(ErrorCodes::NotUsed->value),
                ],

                # Projectable Columns Rules
                0 => [
                    new FalsyRule(ProjectableErrorCodes::Disabled->value),
                    new NotOnListRule(static::VISIBLE_COLUMNS, ProjectableErrorCodes::PNotInColumns->value)
                ],

                # Defined Columns Rules
                1 => [
                    new FalsyRule(DefinedErrorCodes::LaravelDefaultError->value),
                    new NotOnListRule(static::VISIBLE_COLUMNS, DefinedErrorCodes::QNotInColumns->value),
                    new NotOnListRule($variables[$PROJECTABLE_COLUMNS_HEADER], DefinedErrorCodes::QNotInProjectable->value),
                ],
            ],

            items: static::VISIBLE_COLUMNS
        );

        $export = [
            [
                'file' => base_path('truth-table/projection-intersect.csv'),
                'headers' => array_merge($CSV_HEADERS, ['Intersect - Non-strict']),
                'projectionMethod' => 'enableIntersect'
            ],
            [
                'file' => base_path('truth-table/projection-intersect-strict.csv'),
                'headers' => array_merge($CSV_HEADERS, ['Intersect - Strict']),
                'projectionMethod' => 'enableIntersectStrict'
            ],
            [
                'file' => base_path('truth-table/projection-except.csv'),
                'headers' => array_merge($CSV_HEADERS, ['Except - Non-strict']),
                'projectionMethod' => 'enableExcept'
            ],
            [
                'file' => base_path('truth-table/projection-except-strict.csv'),
                'headers' => array_merge($CSV_HEADERS, ['Except - Strict']),
                'projectionMethod' => 'enableExceptStrict'
            ],
            [
                'file' => base_path('truth-table/projection-all.csv'),
                'headers' => array_merge($CSV_HEADERS, [
                    'Intersect - Non-strict', 'Intersect - Strict',
                    'Except - Non-strict', 'Except - Strict',
                ]),
                'projectionMethod' => 'enableAll'
            ]
        ];

        $matrix2D = new Matrix2D($variables);

        foreach ($export as $e) {
            $ptt->{$e['projectionMethod']}(); // enable
            $ptt->export($e['file'], $ptt->truthTable($matrix2D->handle()),
                function($handle) use($e) {
                    fputcsv($handle, ['Projection Truth Table']);
                    fputcsv($handle, $e['headers']);
                }
            );
            $ptt->{$e['projectionMethod']}(false); // disable
        }
    }
}
