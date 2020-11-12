<?php

if ($argc < 2) {
    echo "Usage: php analyze.php logFile\n";
    exit(1);
}

$logFile = $argv[1];
$lastRepo = null;
$failedRepos = [];
foreach (file($logFile) as $line) {
    if (preg_match('/^REPO (.*)$/', $line, $matches)) {
        $lastRepo = $matches[1];
        continue;
    }
    if (preg_match('/AddressSanitizer|Assertion `/', $line)) {
        if ($lastRepo) {
            // Only print last repo once.
            $failedRepos[] = $lastRepo;
            echo $lastRepo, "\n";
            $lastRepo = null;
        }
        echo "    ", $line;
    }
}

// To easily rerun failed repos only.
file_put_contents('failed_repo_list', implode("\n", $failedRepos));
