<?php

namespace Laradigs\Tweaker\Console;

use Illuminate\Console\Command;
use function Laravel\Prompts\multiselect;
use Laradigs\Tweaker\V31\Matrix2D;
use Laradigs\Tweaker\V31\PowerSet;
use Laradigs\Tweaker\V31\ErrorCodes;
use function Laravel\Prompts\select;
use function RGalura\ApiIgniter\base_path;
use Laradigs\Tweaker\V31\TruthTable\Rules\TruthyRule;
use Laradigs\Tweaker\V31\Projection\ProjectionError as E;
use Laradigs\Tweaker\V31\Projection\ProjectionTruthTable;
use Laradigs\Tweaker\V31\TruthTable\Rules\SomeInListRule;

class CreateTruthTableCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:truth-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Truth Table CSV File';

    const TRUTH_TABLE_PROJECTION = 'Projection';

    const TRUTH_TABLE_PROJECTION_OPTIONS = [
        'intersect' => 'Intersect Only',
        'except' => 'Except Only',
        'both' => 'Both Intersect And Except'
    ];

    // all columns of table except hidden columns
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
                'Foo',
                'Bar',
            ])
        )) {
            case static::TRUTH_TABLE_PROJECTION:
                $this->exportProjection(
                    multiselect(
                        label: 'Select Projection Variants:',
                        options: static::TRUTH_TABLE_PROJECTION_OPTIONS,
                        default: ['both']
                    )
                );
                break;
        }
    }

    private function exportProjection(array $options)
    {
        $CSV_HEADERS = ['Projectable Columns (p)', 'Defined Columns (q)', 'Client Input (r)'];

        $variable = array_merge(['*'], (new PowerSet(static::VISIBLE_COLUMNS))->handle());

        $variables = [
            'p' => $variable,
            'q' => $variable,
            'r' => array_filter($variable), // remove falsy value
        ];

        $class = ProjectionTruthTable::class;

        $ptt = new $class(
            rules: [
                // Client Input Rules
                2 => [
                    new TruthyRule(ErrorCodes::NotUsed),
                ],

                // Projectable Columns Rules
                0 => [
                    new TruthyRule(E::P_Disabled),
                    new SomeInListRule(static::VISIBLE_COLUMNS, E::P_NotInColumns),
                ],

                // Defined Columns Rules
                1 => [
                    new TruthyRule(E::Q_LaravelDefaultError),
                    new SomeInListRule(static::VISIBLE_COLUMNS, E::Q_NotInColumns),
                    [SomeInListRule::class, E::Q_NotInProjectable, 'targetIndexAsArgs' => 0],
                ],
            ],

            items: static::VISIBLE_COLUMNS
        );

        $matrix2D = new Matrix2D($variables);

        $export = collect([
            [
                'file' => base_path('truth-table/projection-intersect.csv'),
                'headers' => array_merge($CSV_HEADERS, ['Intersect - Non-strict']),
                'projectionMethod' => 'enabledIntersect',
                'option' => "intersect"
            ],
            [
                'file' => base_path('truth-table/projection-intersect-strict.csv'),
                'headers' => array_merge($CSV_HEADERS, ['Intersect - Strict']),
                'projectionMethod' => 'enabledIntersectStrict',
                'option' => "intersect"
            ],
            [
                'file' => base_path('truth-table/projection-except.csv'),
                'headers' => array_merge($CSV_HEADERS, ['Except - Non-strict']),
                'projectionMethod' => 'enabledExcept',
                'option' => "except"
            ],
            [
                'file' => base_path('truth-table/projection-except-strict.csv'),
                'headers' => array_merge($CSV_HEADERS, ['Except - Strict']),
                'projectionMethod' => 'enabledExceptStrict',
                'option' => "except"
            ],
            [
                'file' => base_path('truth-table/projection.csv'),
                'headers' => array_merge($CSV_HEADERS, [
                    'Intersect - Non-strict', 'Intersect - Strict',
                    'Except - Non-strict', 'Except - Strict',
                ]),
                'projectionMethod' => 'enabledAll',
                'option' => "both"
            ],
        ]);

        $filtered = $export->whereIn('option', $options)->all();

        foreach ($filtered as $row) {
            $ptt->{$row['projectionMethod']}(); // enable
            $ptt->export($row['file'], $ptt->truthTable($matrix2D->handle()),
                function ($handle) use ($row): void {
                    fputcsv($handle, ['Projection Truth Table']);
                    fputcsv($handle, $row['headers']);
                }
            );
            $ptt->{$row['projectionMethod']}(false); // disable
        }
    }
}
