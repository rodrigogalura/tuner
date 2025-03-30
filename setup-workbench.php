<?php

$workbenchPath = __DIR__ . '/workbench';

// // Step 1: Ensure workbench directory exists
// if (!is_dir($workbenchPath)) {
//     mkdir($workbenchPath, 0755, true);
//     echo "✅ Created workbench directory.\n";
// } else {
//     echo "⚡ Workbench directory already exists.\n";
// }

// Step 2: Run Testbench workbench installation (Non-Interactive)
echo "⚡ Running Testbench Workbench Install...\n";
$output = shell_exec(__DIR__ . "/vendor/bin/testbench workbench:install --no-interaction 2>&1");

if (strpos($output, 'Workbench installed successfully') !== false) {
    echo "✅ Testbench Workbench installed successfully.\n";
} else {
    echo "⚠️ Warning: Workbench installation may have encountered issues.\n";
}
echo $output;

// Step 3: Copy custom files into workbench
$customFiles = [
    'app/Http/Controllers/UserController.php',
    'app/Models/User.php',
    'app/Providers/ApiIgniterServiceProvider.php',
    'app/Repositories/UserRepository.php',
    'routes/api.php'
];
foreach ($customFiles as $file) {
    $src = __DIR__ . "/workbench-stubs/$file";
    $dest = $workbenchPath . "/$file";
    if (!file_exists($dest) && file_exists($src)) {
        copy($src, $dest);
        echo "✅ Copied $file to workbench.\n";
    }
}

echo "✅ Workbench setup complete!\n";
