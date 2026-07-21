<?php

declare(strict_types=1);

/**
 *
 * @file app.php
 * @author Gaspard Kirira
 *
 * Copyright 2026, Gaspard Kirira.
 * All rights reserved.
 * https://github.com/iviphp/ivi
 *
 * Use of this source code is governed by an MIT license
 * that can be found in the LICENSE file.
 *
 * IviPHP
 *
 */

use App\Console\Commands\AboutCommand;
use App\Providers\AppServiceProvider;
use Ivi\Config\Config;
use Ivi\Framework\Framework;

$basePath = dirname(__DIR__);

$autoloadPath = $basePath
    . DIRECTORY_SEPARATOR
    . 'vendor'
    . DIRECTORY_SEPARATOR
    . 'autoload.php';

if (!is_file($autoloadPath)) {
    throw new RuntimeException(
        'Composer dependencies are not installed. Run "composer install".'
    );
}

require_once $autoloadPath;

$environmentPath = $basePath
    . DIRECTORY_SEPARATOR
    . '.env';

if (is_file($environmentPath)) {
    $variables = parse_ini_file(
        $environmentPath,
        false,
        INI_SCANNER_RAW
    );

    if ($variables === false) {
        throw new RuntimeException(
            'Unable to parse the application environment file.'
        );
    }

    foreach ($variables as $name => $value) {
        if (!is_string($name)) {
            continue;
        }

        $name = trim($name);

        if (
            $name === ''
            || preg_match(
                '/^[A-Za-z_][A-Za-z0-9_]*$/',
                $name
            ) !== 1
        ) {
            continue;
        }

        if (
            array_key_exists($name, $_ENV)
            || array_key_exists($name, $_SERVER)
            || getenv($name) !== false
        ) {
            continue;
        }

        if (!is_scalar($value) && $value !== null) {
            continue;
        }

        $value = (string) $value;

        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;

        putenv(
            $name . '=' . $value
        );
    }
}

$config = Config::load(
    $basePath
    . DIRECTORY_SEPARATOR
    . 'config'
);

$applicationName = (string) $config->get(
    'app.name',
    'Ivi Application'
);

$applicationVersion = (string) $config->get(
    'app.version',
    '0.1.0'
);

$environment = (string) $config->get(
    'app.environment',
    'production'
);

$framework = Framework::create(
    basePath: $basePath,
    config: $config,
    environment: $environment,
    consoleName: $applicationName,
    consoleVersion: $applicationVersion
);

$framework->provider(
    AppServiceProvider::class
);

$framework->registerCommand(
    new AboutCommand()
);

return $framework;
