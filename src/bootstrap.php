<?php

$php_version = phpversion();
if (version_compare($php_version, '7.1.0', '<')) {
    fprintf(STDERR, "Pahout requires PHP version 7.1.0 or newer. The installed version is $php_version.\n");
    exit(1);
}

if (extension_loaded('ast')) {
    $ast_version = phpversion('ast');
    if (version_compare($ast_version, '0.1.4', '<')) {
        fprintf(STDERR, "php-ast extension was found. But, Pahout requires php-ast version 0.1.4 or newer. The installed version is $ast_version.\n");
        exit(1);
    }
} else {
    fprintf(STDERR, "php-ast extension could not be found. Pahout requires php-ast version 0.1.4 or newer.\n");
    exit(1);
}

if (!file_exists('vendor/autoload.php')) {
    fprintf(STDERR, "Autoload file could not be found. Please run `composer install` at first.\n");
    exit(1);
}

require_once 'vendor/autoload.php';

use Symfony\Component\Console\Application;
use Pahout\Command\Check;

$check = new Check();

$app = new Application('Pahout', '0.1.0');
$app->add($check);
$app->setDefaultCommand($check->getName(), true);
$app->run();
