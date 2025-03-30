<?php

$workbenchPath = __DIR__.'/workbench';
$stubsPath = __DIR__.'/workbench-stubs';
$testbenchConfig = __DIR__.'/testbench.yaml';

// Step 1: Ensure workbench directory exists
if (! is_dir($workbenchPath)) {
    mkdir($workbenchPath, 0755, true);
    echo "✅ Created workbench directory.\n";
} else {
    echo "⚡ Workbench directory already exists.\n";
}

// Step 2: Create testbench.yaml file
if (! file_exists($testbenchConfig)) {
    $yamlContent = <<<YAML
laravel: '@testbench'

providers:
  - Workbench\App\Providers\ApiIgniterServiceProvider

migrations:
  - workbench/database/migrations

seeders:
  - Workbench\Database\Seeders\DatabaseSeeder

workbench:
  start: '/'
  install: true
  health: false
  discovers:
    api: true
    factories: true
  build:
    - asset-publish
    - create-sqlite-db
    - db-wipe
    - migrate-fresh

YAML;

    file_put_contents($testbenchConfig, $yamlContent);
    echo "✅ Created testbench.yaml configuration.\n";
} else {
    echo "⚡ testbench.yaml already exists. Skipping creation.\n";
}

// Step 3: Recursively Copy Stubs into Workbench
function copyRecursive($src, $dst)
{
    $dir = opendir($src);
    if (! is_dir($dst)) {
        mkdir($dst, 0755, true);
    }
    while (($file = readdir($dir)) !== false) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        $srcFile = $src.'/'.$file;
        $dstFile = $dst.'/'.$file;
        if (is_dir($srcFile)) {
            copyRecursive($srcFile, $dstFile); // Recursively copy subdirectories
        } else {
            copy($srcFile, $dstFile);
            echo "✅ Copied $srcFile to $dstFile\n";
        }
    }
    closedir($dir);
}

// Copy everything from `workbench-stubs/` to `workbench/`
if (is_dir($stubsPath)) {
    copyRecursive($stubsPath, $workbenchPath);
    echo "✅ All stubs copied successfully.\n";
} else {
    echo "⚠️ No workbench-stubs directory found. Skipping stub copy.\n";
}

echo "✅ Workbench setup complete!\n";
