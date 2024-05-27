<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;
use Tests\Utils\Constants;

require dirname(__DIR__) . '/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(Constants::rootDir() . '/.env');
}

ini_set('memory_limit', -1);

if (function_exists('xdebug_set_filter')) {
    xdebug_set_filter(
        XDEBUG_FILTER_CODE_COVERAGE,
        XDEBUG_PATH_INCLUDE,
        [
            Constants::srcDir(),
        ]
    );
}

require dirname(__DIR__) . '/vendor/squizlabs/php_codesniffer/tests/bootstrap.php';
