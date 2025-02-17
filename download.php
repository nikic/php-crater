<?php error_reporting(E_ALL);

require 'vendor/autoload.php';

function getTopPackages(int $min, int $max) {
    $perPage = max(1, min($max - $min, 100));
    $page = intdiv($min, $perPage);
    $id = $page * $perPage;
    while (true) {
        $page++;
        $url = 'https://packagist.org/explore/popular.json?page=' . $page.'&per_page='.$perPage;
        $json = json_decode(file_get_contents($url), true);
        foreach ($json['packages'] as $package) {
            yield $id => $package['name'];
            $id++;
            if ($id >= $max) {
                return;
            }
        }
    }
}

if ($argc < 3) {
    echo "Usage: download.php min-package max-package\n";
    return;
}

$repoListFileName = __DIR__ . '/downloaded_repo_list';
$repoListFile = fopen($repoListFileName, 'a');

$minPackage = $argv[1];
$maxPackage = $argv[2];
foreach (getTopPackages($minPackage, $maxPackage) as $i => $packageName) {
    echo "[$i] $packageName\n";
    $packageName = strtolower($packageName);
    $url = 'https://repo.packagist.org/p2/' . $packageName . '~dev.json';
    $json = json_decode(file_get_contents($url), true);
    $versions = \Composer\MetadataMinifier\MetadataMinifier::expand($json['packages'][$packageName]);
    unset($package);
    // select default branch if available
    foreach ($versions as $version) {
        if (true === ($version['default-branch'] ?? false)) {
            $package = $version;
            break;
        }
    }
    if (!isset($package)) {
        // or any branch
        if (count($versions) > 0) {
            $package = reset($versions);
        } else {
            // or any version
            $url = 'https://repo.packagist.org/p2/' . $packageName . '~dev.json';
            $json = json_decode(file_get_contents($url), true);
            $versions = \Composer\MetadataMinifier\MetadataMinifier::expand($json['packages'][$packageName]);
            if (count($versions) > 0) {
                $package = reset($versions);
            } else {
                echo "Skipping due to no version found\n";
                continue;
            }
        }
    }

    // Thanks to people excluding tests from dist packages,
    // we're forced to clone source repos here.
    if (($package['source'] ?? null) === null) {
        echo "Skipping due to missing source\n";
        continue;
    }
    if ($package['source']['type'] !== 'git') {
        echo "Unexpected source type: ", $package['source']['type'], "\n";
        continue;
    }

    $git = $package['source']['url'];
    $repo = __DIR__ . '/repos/' . $packageName;
    if (!is_dir($repo)) {
        echo "Cloning $packageName @ {$package['version']} from $git...\n";
        exec("git clone $git $repo", $execOutput, $execRetval);
        if ($execRetval !== 0) {
            echo "git clone failed: $execOutput\n";
            break;
        }
    }

    fwrite($repoListFile, 'repos/' . $packageName . "\n");
}
