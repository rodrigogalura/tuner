<?php

namespace Laradigs\Tweaker\Console;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use function Laravel\Prompts\select;
use Laradigs\Tweaker\TruthTableGenerator\ProjectionCSV;

class CreateTruthTableCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
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
        $options = Str::of(IntersectProjection::class)
            ->classBasename()
            ->title();

        dd($options);


        $option = $this->argument('option')
            ?? select(
                label: 'Select option do you want to generate CSV:',
                options: array_map('class_basename', [
                    IntersectProjection::class
                ])
                // options: [
                //     class_basename(IntersectProjection::class)
                //     // ProjectionCSV::PROJECTION_NAME,
                //     // ProjectionCSV::PROJECTION_EXCEPT_NAME,
                // ],
            );

        $this->info($option);

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

    private function copyToClipboard($file)
    {
        $resultType = select(
            label: 'What result type do you want to copy to clipboard?',
            options: [
                ProjectionCSV::PROJECTION_INTERSECT_NAME,
                ProjectionCSV::PROJECTION_EXCEPT_NAME,
            ],
        );

        $appPath = dirname(dirname(__DIR__));

        if (!$file) {
            $this->warning("Missing argument.\n");
            exit(1);
        }

        // Detect OS
        $os = php_uname('s');

        // Prepare the command to run exporter.php
        $command = escapeshellcmd("php {$appPath}/truth-table/exporter.php {$file} {$resultType}");

        switch (true) {
            case stripos($os, 'Darwin') !== false:
                // macOS
                $fullCommand = "{$command} | pbcopy";
                break;

            case stripos($os, 'Linux') !== false:
                // Linux
                $fullCommand = "{$command} | xclip -selection clipboard";
                break;

            case stripos($os, 'MINGW') !== false || stripos($os, 'CYGWIN') !== false || stripos($os, 'MSYS') !== false || stripos($os, 'Windows') !== false:
                // Windows (Git Bash or others)
                $fullCommand = "{$command} | clip";
                break;

            default:
                $this->warning("Unsupported OS: {$os}\n");
                exit(1);
        }

        // Execute the command
        exec($fullCommand, $output, $resultCode);

        if ($resultCode === 0) {
            $this->info("✅ Copied to clipboard!\n");
        } else {
            $this->warning("❌ Failed to copy to clipboard. Error code: {$resultCode}\n");
        }
    }
}
