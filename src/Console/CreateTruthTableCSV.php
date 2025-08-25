<?php

namespace Laradigs\Tweaker\Console;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Laradigs\Tweaker\V31\Matrix2D;
use Laradigs\Tweaker\V31\PowerSet;
use function Laravel\Prompts\select;
use function RGalura\ApiIgniter\base_path;
use Laradigs\Tweaker\V31\TruthTable\Rules\FalsyRule;
use Laradigs\Tweaker\TruthTableGenerator\ProjectionCSV;
use Laradigs\Tweaker\V31\TruthTable\Rules\NotOnListRule;
use Laradigs\Tweaker\V31\Projection\ProjectionTruthTable;

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

    /**
     * Execute the console command.
     */
    public function handle()
    {
        # all columns of table except hidden columns
        $visibleColumns = ['id', 'name'];

        $variable = array_merge(['*'], (new PowerSet($visibleColumns))->handle());
        $variables = [
            'Projectable Columns (p)' => $variable,
            'Defined Columns (q)' => $variable,

            # Client Input must be last on variables
            'Client Input (r)' => $variable,
        ];

        $matrix2D = new Matrix2D($variables);

        // $options = Str::of(IntersectProjection::class)
        //     ->classBasename()
        //     ->title();

        // dd($options);

        switch (select(
            label: 'Select the Truth Table do you want to generate CSV:',
            options: array_map('class_basename', [
                static::TRUTH_TABLE_PROJECTION,
                "Foo",
                "Bar"
            ])
        )) {
            case static::TRUTH_TABLE_PROJECTION:
                $ptt = new ProjectionTruthTable(
                    rules:
                    [
                        # Client Input Rules
                        2 => [
                            new FalsyRule(0),
                        ],

                        # Projectable Columns Rules
                        0 => [
                            new FalsyRule(2),
                            new NotOnListRule($visibleColumns, 3)
                        ],

                        # Defined Columns Rules
                        1 => [
                            new FalsyRule(4),
                            new NotOnListRule($visibleColumns, 5),
                            new NotOnListRule($variables['Projectable Columns (p)'], 6),
                        ],
                    ],

                    items: $visibleColumns
                );

                $export = [
                    [
                        'file' => base_path('truth-table/projection-intersect.csv'),
                        'headers' => ['Projectable (p)', 'Defined (q)', 'Client (r)', 'Intersect - Non-strict'],
                        'projectionMethod' => 'enableIntersect'
                    ],
                    [
                        'file' => base_path('truth-table/projection-intersect-strict.csv'),
                        'headers' => ['Projectable (p)', 'Defined (q)', 'Client (r)', 'Intersect - Strict'],
                        'projectionMethod' => 'enableIntersectStrict'
                    ],
                    [
                        'file' => base_path('truth-table/projection-except.csv'),
                        'headers' => ['Projectable (p)', 'Defined (q)', 'Client (r)', 'Except - Non-strict'],
                        'projectionMethod' => 'enableExcept'
                    ],
                    [
                        'file' => base_path('truth-table/projection-except-strict.csv'),
                        'headers' => ['Projectable (p)', 'Defined (q)', 'Client (r)', 'Except - Strict'],
                        'projectionMethod' => 'enableExceptStrict'
                    ],
                    [
                        'file' => base_path('truth-table/projection-all.csv'),
                        'headers' => [
                            'Projectable (p)', 'Defined (q)', 'Client (r)',
                            'Intersect - Non-strict', 'Intersect - Strict',
                            'Except - Non-strict', 'Except - Strict',
                        ],
                        'projectionMethod' => 'enableAll'
                    ]
                ];

                foreach ($export as $e) {
                    $ptt->{$e['projectionMethod']}(true); // enable
                    $ptt->export($e['file'], $ptt->truthTable($matrix2D->handle()),
                        function($handle) use($e) {
                            fputcsv($handle, ['Projection Truth Table']);
                            fputcsv($handle, $e['headers']);
                        }
                    );
                    $ptt->{$e['projectionMethod']}(false); // disable
                }
                break;
        }
    }
}
