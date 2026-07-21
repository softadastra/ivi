<?php

declare(strict_types=1);

/**
 *
 * @file database.php
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

$integer = static function (
    string $name,
    int $default
) use ($environment): int {
    $value = $environment($name);

    if (
        $value === null
        || filter_var(
            $value,
            FILTER_VALIDATE_INT
        ) === false
    ) {
        return $default;
    }

    return (int) $value;
};

$basePath = dirname(__DIR__);

$databasePath = static function (
    string $path
) use ($basePath): string {
    $path = trim($path);

    if ($path === '') {
        return $basePath
            . DIRECTORY_SEPARATOR
            . 'storage'
            . DIRECTORY_SEPARATOR
            . 'database.sqlite';
    }

    $normalized = str_replace(
        '\\',
        '/',
        $path
    );

    if (
        str_starts_with($normalized, '/')
        || preg_match(
            '/^[A-Za-z]:\//',
            $normalized
        ) === 1
    ) {
        return $path;
    }

    return $basePath
        . DIRECTORY_SEPARATOR
        . str_replace(
            '/',
            DIRECTORY_SEPARATOR,
            $normalized
        );
};

return [
    'default' => (string) $environment(
        'DB_DRIVER',
        'sqlite'
    ),

    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',

            'database' => $databasePath(
                (string) $environment(
                    'DB_DATABASE',
                    'storage/database.sqlite'
                )
            ),

            'prefix' => '',

            'foreign_keys' => true,

            'busy_timeout' => 5000,

            'journal_mode' => 'WAL',

            'synchronous' => 'NORMAL',
        ],

        'mysql' => [
            'driver' => 'mysql',

            'host' => (string) $environment(
                'DB_HOST',
                '127.0.0.1'
            ),

            'port' => $integer(
                'DB_PORT',
                3306
            ),

            'database' => (string) $environment(
                'DB_NAME',
                'ivi'
            ),

            'username' => (string) $environment(
                'DB_USERNAME',
                'root'
            ),

            'password' => (string) $environment(
                'DB_PASSWORD',
                ''
            ),

            'charset' => 'utf8mb4',

            'collation' => 'utf8mb4_unicode_ci',

            'prefix' => '',

            'strict' => true,
        ],

        'pgsql' => [
            'driver' => 'pgsql',

            'host' => (string) $environment(
                'DB_HOST',
                '127.0.0.1'
            ),

            'port' => $integer(
                'DB_PORT',
                5432
            ),

            'database' => (string) $environment(
                'DB_NAME',
                'ivi'
            ),

            'username' => (string) $environment(
                'DB_USERNAME',
                'postgres'
            ),

            'password' => (string) $environment(
                'DB_PASSWORD',
                ''
            ),

            'charset' => 'utf8',

            'schema' => 'public',

            'prefix' => '',
        ],
    ],
];
