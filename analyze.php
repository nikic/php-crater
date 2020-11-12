<?php

$logFile = $argv[1] ?? "log";
if (!file_exists($logFile)) {
    echo "Log file \"$logFile\" does not exist.\n";
    exit(1);
}

$fullLog = file_get_contents($logFile);
$split = preg_split('/^REPO (.*)$/m', $fullLog, -1, PREG_SPLIT_DELIM_CAPTURE);
$failedRepos = [];
foreach (array_chunk(array_slice($split, 1), 2) as [$repo, $log]) {
    $interesting = extractInterestingLog($log);
    if ($interesting) {
        $failedRepos[] = $repo;
        echo $repo;
        echo str_replace("\n", "\n    ", "\n" . $interesting);
        echo "\n\n";
    }
}

// To easily rerun failed repos only.
file_put_contents('failed_repo_list', implode("\n", $failedRepos));

function extractInterestingLog(string $log): ?string {
    $interesting = [];
    foreach (explode("\n", $log) as $line) {
        if (preg_match('/AddressSanitizer|Assertion `/', $line)) {
            $interesting[] = $line;
        }
    }
    return implode("\n", $interesting);
}
