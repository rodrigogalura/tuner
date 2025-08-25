<?php

namespace Laradigs\Tweaker\Console;

use Illuminate\Console\Command;
use function Laravel\Prompts\select;
use function RGalura\ApiIgniter\base_path;

class CopyToClipboardTheTruthTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy-to-clipboard:truth-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy to clipboard the trutht table';

    const MAC_COPY = 'pbcopy';
    const LINUX_COPY = 'xclip -selection clipboard';
    const WIN_COPY = 'clip';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $files = glob(base_path('truth-table') . '/*.csv');

        if (empty($files)) {
            $this->info('No available CSV files.');
            exit;
        }

        $file = select(
            label: 'Select the Truth Table do you want to copy to clipboard:',
            options: array_map('basename', $files),
        );

        // Detect OS
        $os = php_uname('s');

        // Prepare the command to run exporter.php
        $command = escapeshellcmd("php " . base_path('truth-table/exporter.php') . " {$file}");

        switch (true) {
            case stripos($os, 'Darwin') !== false:
                // macOS
                $fullCommand = "{$command} | " . static::MAC_COPY;
                break;

            case stripos($os, 'Linux') !== false:
                // Linux
                $fullCommand = "{$command} | " . static::LINUX_COPY;
                break;

            case stripos($os, 'MINGW') !== false || stripos($os, 'CYGWIN') !== false || stripos($os, 'MSYS') !== false || stripos($os, 'Windows') !== false:
                // Windows (Git Bash or others)
                $fullCommand = "{$command} | " . static::WIN_COPY;
                break;

            default:
                $this->warning("Unsupported OS: {$os}\n");
                exit;
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
