<?php
$composerJson = file_get_contents('./composer.json');
$composerJson = json_decode($composerJson, true);

$suggest = $composerJson['suggest'];

$opt = getopt('i::d::', ['install', 'uninstall']);
// this is install
if (isset($opt['i'])) {
    foreach ($suggest as $package => $version) {
        if (str_contains( $package, '/')) {
            $command = 'composer require ' . $package;
            shell_exec($command);
        }

        $command = 'composer require symfony/finder:^5.0';
        shell_exec($command);
    }
}

// this is uninstall
if (isset($opt['d'])) {
    foreach ($suggest as $package => $version) {
        if (str_contains( $package, '/')) {
            $command = 'composer remove ' . $package;
            shell_exec($command);
        }

        $command = 'composer remove symfony/finder';
        shell_exec($command);
    }
}
