<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;
use Tests\Utils\Constants;

require dirname(__DIR__) . '/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(Constants::rootDir() . '/.env');
}

require dirname(__DIR__) . '/vendor/squizlabs/php_codesniffer/tests/bootstrap.php';
