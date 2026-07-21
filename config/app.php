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

$environment = static function (
    string $name,
    mixed $default = null
): mixed {
    $value = getenv($name);

    if ($value === false) {
        return $default;
    }

    return $value;
};

$boolean = static function (
    string $name,
    bool $default = false
) use ($environment): bool {
    $value = $environment($name);

    if ($value === null) {
        return $default;
    }

    $parsed = filter_var(
        $value,
        FILTER_VALIDATE_BOOLEAN,
        FILTER_NULL_ON_FAILURE
    );

    return $parsed ?? $default;
};

return [
    'name' => (string) $environment(
        'APP_NAME',
        'Ivi Application'
    ),

    'version' => '0.1.0',

    'environment' => (string) $environment(
        'APP_ENV',
        'production'
    ),

    'debug' => $boolean(
        'APP_DEBUG',
        false
    ),

    'url' => rtrim(
        (string) $environment(
            'APP_URL',
            'http://localhost:8000'
        ),
        '/'
    ),

    'timezone' => (string) $environment(
        'APP_TIMEZONE',
        'UTC'
    ),

    'locale' => (string) $environment(
        'APP_LOCALE',
        'en'
    ),
];
