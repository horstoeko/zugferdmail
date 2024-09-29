<?php

use Composer\InstalledVersions as ComposerInstalledVersions;
use horstoeko\zugferdmail\console\ZugferdMailListFoldersConsoleCommand;
use horstoeko\zugferdmail\console\ZugferdMailProcessFoldersConsoleCommand;
use Symfony\Component\Console\Application;

$autoloadFiles = [
    __DIR__ . '/../../vendor/autoload.php',
    __DIR__ . '/vendor/autoload.php',
];

foreach ($autoloadFiles as $autoloadFile) {
    if (file_exists($autoloadFile)) {
        include_once $autoloadFile;
        break;
    }
}

$app = new Application('ZugferdMail', ComposerInstalledVersions::getVersion('horstoeko/zugferdmail'));
$app->add(new ZugferdMailListFoldersConsoleCommand());
$app->add(new ZugferdMailProcessFoldersConsoleCommand());
$app->run();
