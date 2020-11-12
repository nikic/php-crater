<?php error_reporting(E_ALL);

function getTopPackages($min, $max) {
    $perPage = 15;
    $page = intdiv($min, $perPage);
    $id = $page * $perPage;
    while (true) {
        $page++;
        $url = 'https://packagist.org/explore/popular.json?page=' . $page;
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

$minPackage = $argv[1];
$maxPackage = $argv[2];
foreach (getTopPackages($minPackage, $maxPackage) as $i => $packageName) {
    echo "[$i] $packageName\n";
    $packageName = strtolower($packageName);
    $url = 'https://repo.packagist.org/p/' . $packageName . '.json';
    $json = json_decode(file_get_contents($url), true);
    $versions = $json['packages'][$packageName];
    if (isset($versions['dev-master'])) {
        $version = 'dev-master';
    } else {
        // Pick last version.
        $keys = array_keys($versions);
        $version = $keys[count($keys) - 1];
    }

    // Thanks to people excluding tests from dist packages,
    // we're forced to clone source repos here.
    $package = $versions[$version];
    if ($package['source'] === null) {
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
        echo "Cloning $packageName @ $version from $git...\n";
        exec("git clone $git $repo", $execOutput, $execRetval);
        if ($execRetval !== 0) {
            echo "git clone failed: $execOutput\n";
            break;
        }
    }
}
