<?php

namespace Laradigs\Tweaker\Console;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Laradigs\Tweaker\V31\Matrix2D;
use Laradigs\Tweaker\V31\PowerSet;
use Laradigs\Tweaker\V31\ErrorCodes;
use function Laravel\Prompts\select;
use function RGalura\ApiIgniter\base_path;
use Laradigs\Tweaker\V31\TruthTable\Rules\TruthyRule;
use Laradigs\Tweaker\V31\Projection\DefinedErrorCodes;
use Laradigs\Tweaker\TruthTableGenerator\ProjectionCSV;
use Laradigs\Tweaker\V31\Projection\ProjectionError as E;
use Laradigs\Tweaker\V31\Projection\ProjectionTruthTable;
use Laradigs\Tweaker\V31\TruthTable\Rules\SomeInListRule;
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
        $CSV_HEADERS = ['Projectable Columns (p)', 'Defined Columns (q)', 'Client Input (r)'];

        $variable = array_merge(['*'], (new PowerSet(static::VISIBLE_COLUMNS))->handle());

        $variables = [
            'p' => $variable,
            'q' => $variable,
            'r' => array_filter($variable) // remove falsy value
        ];

        $class = ProjectionTruthTable::class;

        $ptt = new $class(
            rules:
            [
                # Client Input Rules
                2 => [
                    new TruthyRule(ErrorCodes::NotUsed),
                ],

                # Projectable Columns Rules
                0 => [
                    new TruthyRule(E::P_Disabled),
                    new SomeInListRule(static::VISIBLE_COLUMNS, E::P_NotInColumns)
                ],

                # Defined Columns Rules
                1 => [
                    new TruthyRule(E::Q_LaravelDefaultError),
                    new SomeInListRule(static::VISIBLE_COLUMNS, E::Q_NotInColumns),
                    [
                        'classRule' => SomeInListRule::class,
                        'targetArgsIndex' => 0,
                        'errorEnum' => E::Q_NotInProjectable
                    ]
                ],
            ],

            items: static::VISIBLE_COLUMNS
        );

        $debug = 0;
        if ($debug) {
            $ptt->enabledAll(); // enable
            $ptt->export(base_path('truth-table/debug.csv'), $ptt->truthTable([
                ['id', '*', '*']
            ]),
                function($handle) use($CSV_HEADERS) {
                    fputcsv($handle, ['Projection Truth Table']);
                    fputcsv($handle, array_merge($CSV_HEADERS, ['Intersect - Non-strict']));
                }
            );
            $ptt->enabledAll(false); // disable
        } else {
            $matrix2D = new Matrix2D($variables);

            $export = [
                [
                    'file' => base_path('truth-table/projection-intersect.csv'),
                    'headers' => array_merge($CSV_HEADERS, ['Intersect - Non-strict']),
                    'projectionMethod' => 'enabledIntersect'
                ],
                [
                    'file' => base_path('truth-table/projection-intersect-strict.csv'),
                    'headers' => array_merge($CSV_HEADERS, ['Intersect - Strict']),
                    'projectionMethod' => 'enabledIntersectStrict'
                ],
                [
                    'file' => base_path('truth-table/projection-except.csv'),
                    'headers' => array_merge($CSV_HEADERS, ['Except - Non-strict']),
                    'projectionMethod' => 'enabledExcept'
                ],
                [
                    'file' => base_path('truth-table/projection-except-strict.csv'),
                    'headers' => array_merge($CSV_HEADERS, ['Except - Strict']),
                    'projectionMethod' => 'enabledExceptStrict'
                ],
                [
                    'file' => base_path('truth-table/projection-all.csv'),
                    'headers' => array_merge($CSV_HEADERS, [
                        'Intersect - Non-strict', 'Intersect - Strict',
                        'Except - Non-strict', 'Except - Strict',
                    ]),
                    'projectionMethod' => 'enabledAll'
                ]
            ];

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
}
