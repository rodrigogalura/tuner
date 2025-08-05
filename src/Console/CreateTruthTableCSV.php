<?php

namespace Laradigs\Tweaker\Console;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use function Laravel\Prompts\select;
use function RGalura\ApiIgniter\base_path;
use Laradigs\Tweaker\V31\TruthTable\Rules\FalsyRule;
use Laradigs\Tweaker\TruthTableGenerator\ProjectionCSV;
use Laradigs\Tweaker\V31\TruthTable\Rules\NotOnListRule;

class CreateTruthTableCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'create:truth-table {option?} {--copy}';
    protected $signature = 'create:truth-table {option?} {--copy}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Truth Table CSV File';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        # all columns of table except hidden columns
        $visibleColumns = ['id', 'name'];

        $variables = [
            'Projectable Columns (p)' => ['*', 'id', 'name', 'id, name', ''],
            'Defined Columns (q)' => ['*', 'id', 'name', 'id, name', ''],

            # Client Input must be last on variables
            'Client Input (r)' => ['*', 'id', 'name', 'id, name', ''],
        ];

        $truthTable = new \Laradigs\Tweaker\V31\TruthTable\TruthTable(
            rules:
            [
                # Client Input Rules
                2 => [
                    new FalsyRule(0),
                ],

                # Projectable Columns Rules
                0 => [
                    new FalsyRule(1),
                    new NotOnListRule($visibleColumns, 2)
                ],

                # Defined Columns Rules
                1 => [
                    new FalsyRule(3),
                    new NotOnListRule($visibleColumns, 4),
                    new NotOnListRule($variables['Projectable Columns (p)'], 5),
                ],
            ],

            asteriskValues: $visibleColumns
        );

        $matrix2d = $truthTable->matrix2d($variables);
        $matrixProjection = $truthTable->matrixProjection($matrix2d);

        $truthTable->export(base_path('truth-table/projection.csv'), $matrixProjection,
            function($handle) {
                fputcsv($handle, ['Truth Table']);
                fputcsv($handle, [
                    'Projectable (p)', 'Defined (q)', 'Client (r)',
                    'Intersect - Non-strict',
                    'Intersect - Strict',
                    'Except - Non-strict',
                    'Except - Strict',
                ]);
            }
        );

        // $matrix = $truthTable->matrix($variables);


        // $options = Str::of(IntersectProjection::class)
        //     ->classBasename()
        //     ->title();

        // dd($options);

        // $option = $this->argument('option')
        //     ?? select(
        //         label: 'Select option do you want to generate CSV:',
        //         options: array_map('class_basename', [
        //             IntersectProjection::class
        //         ])
        //         // options: [
        //         //     class_basename(IntersectProjection::class)
        //         //     // ProjectionCSV::PROJECTION_NAME,
        //         //     // ProjectionCSV::PROJECTION_EXCEPT_NAME,
        //         // ],
        //     );

        // $this->info($option);

        // $file = Str::kebab($option).".csv";

        // switch ($option) {
        //     case ProjectionCSV::PROJECTION_NAME:
        //         (new ProjectionCSV($file))
        //             ->intersectCSV()
        //             ->generate();
        //         break;

        //     // case ProjectionCSV::PROJECTION_EXCEPT_NAME:
        //     //     (new ProjectionCSV($file))
        //     //         ->exceptCSV()
        //     //         ->generate();
        //     //     break;
        // }

        // if ($this->option('copy')) {
        //     $this->copyToClipboard($file);
        // }
    }

    // private function copyToClipboard($file)
    // {
    //     $resultType = select(
    //         label: 'What result type do you want to copy to clipboard?',
    //         options: [
    //             ProjectionCSV::PROJECTION_INTERSECT_NAME,
    //             ProjectionCSV::PROJECTION_EXCEPT_NAME,
    //         ],
    //     );

    //     $appPath = dirname(dirname(__DIR__));

    //     if (!$file) {
    //         $this->warning("Missing argument.\n");
    //         exit(1);
    //     }

    //     // Detect OS
    //     $os = php_uname('s');

    //     // Prepare the command to run exporter.php
    //     $command = escapeshellcmd("php {$appPath}/truth-table/exporter.php {$file} {$resultType}");

    //     switch (true) {
    //         case stripos($os, 'Darwin') !== false:
    //             // macOS
    //             $fullCommand = "{$command} | pbcopy";
    //             break;

    //         case stripos($os, 'Linux') !== false:
    //             // Linux
    //             $fullCommand = "{$command} | xclip -selection clipboard";
    //             break;

    //         case stripos($os, 'MINGW') !== false || stripos($os, 'CYGWIN') !== false || stripos($os, 'MSYS') !== false || stripos($os, 'Windows') !== false:
    //             // Windows (Git Bash or others)
    //             $fullCommand = "{$command} | clip";
    //             break;

    //         default:
    //             $this->warning("Unsupported OS: {$os}\n");
    //             exit(1);
    //     }

    //     // Execute the command
    //     exec($fullCommand, $output, $resultCode);

    //     if ($resultCode === 0) {
    //         $this->info("✅ Copied to clipboard!\n");
    //     } else {
    //         $this->warning("❌ Failed to copy to clipboard. Error code: {$resultCode}\n");
    //     }
    // }
}
