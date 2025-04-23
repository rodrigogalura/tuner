<?php

$workbenchPath = __DIR__.'/workbench';
$collectionPath = __DIR__.'/bruno-collection';

$workbenchStubsPath = __DIR__.'/stubs/workbench';
$collectionStubsPath = __DIR__.'/stubs/bruno-collection';

$testbenchConfig = __DIR__.'/testbench.yaml';

// Step 1: Ensure workbench and bruno-collection directory exists
if (! is_dir($workbenchPath)) {
    mkdir($workbenchPath, 0755, true);
    echo "✅ Created workbench directory.\n";
} else {
    echo "⚡ Workbench directory already exists.\n";
}

if (! is_dir($collectionPath)) {
    mkdir($collectionPath, 0755, true);
    echo "✅ Created bruno-collection directory.\n";
} else {
    echo "⚡ The bruno-collection directory already exists.\n";
}

// Step 2: Create testbench.yaml file
if (! file_exists($testbenchConfig)) {
    $yamlContent = <<<YAML
laravel: '@testbench'

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

// Copy everything from `stubs/workbench/` to `workbench/`
if (is_dir($workbenchStubsPath)) {
    copyRecursive($workbenchStubsPath, $workbenchPath);
    echo "✅ All workbench stubs copied successfully.\n";
} else {
    echo "⚠️ No stubs/workbench directory found. Skipping stub copy.\n";
}

// Copy everything from `stubs/bruno-collection/` to `bruno-collection/`
if (is_dir($collectionStubsPath)) {
    copyRecursive($collectionStubsPath, $collectionPath);
    echo "✅ All collection stubs copied successfully.\n";
} else {
    echo "⚠️ No stubs/bruno-collection directory found. Skipping stub copy.\n";
}

echo "✅ Development setup complete!\n";
