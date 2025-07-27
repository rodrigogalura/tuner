<?php

namespace Laradigs\Tweaker\Console;

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
    protected $signature = 'create:truth-table';

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
        $option  = select(
            label: 'Select option do you want to generate CSV:',
            options: [
                ProjectionCSV::PROJECTION_NAME
            ],
        );

        switch ($option) {
            case ProjectionCSV::PROJECTION_NAME:
                (new ProjectionCSV)
                    ->intersect()
                    ->generate();
                break;

            default:
                $this->warning("Invalid selected option");
                break;
        }

        $this->info($option);
    }
}
