<?php

declare(strict_types=1);

/**
 *
 * @file index.php
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

use Ivi\Framework\Framework;

$basePath = dirname(__DIR__);

try {
    $framework = require $basePath
        . DIRECTORY_SEPARATOR
        . 'bootstrap'
        . DIRECTORY_SEPARATOR
        . 'app.php';

    if (!$framework instanceof Framework) {
        throw new RuntimeException(
            'The application bootstrap file must return an IviPHP Framework instance.'
        );
    }

    $framework->start();

    $method = strtoupper(
        $_SERVER['REQUEST_METHOD']
            ?? 'GET'
    );

    $uri = $_SERVER['REQUEST_URI']
        ?? '/';

    $path = parse_url(
        $uri,
        PHP_URL_PATH
    );

    if (!is_string($path) || $path === '') {
        $path = '/';
    }

    if ($method === 'GET' && $path === '/health') {
        http_response_code(200);

        header(
            'Content-Type: application/json; charset=UTF-8'
        );

        echo json_encode(
            [
                'status' => 'ok',
                'application' => (string) $framework
                    ->config()
                    ->get(
                        'app.name',
                        'Ivi Application'
                    ),
                'environment' => $framework
                    ->environment(),
                'php' => PHP_VERSION,
            ],
            JSON_PRETTY_PRINT
            | JSON_UNESCAPED_SLASHES
            | JSON_THROW_ON_ERROR
        );

        exit;
    }

    if ($method === 'GET' && $path === '/') {
        $applicationName = htmlspecialchars(
            (string) $framework
                ->config()
                ->get(
                    'app.name',
                    'Ivi Application'
                ),
            ENT_QUOTES
            | ENT_SUBSTITUTE,
            'UTF-8'
        );

        $applicationVersion = htmlspecialchars(
            (string) $framework
                ->config()
                ->get(
                    'app.version',
                    '0.1.0'
                ),
            ENT_QUOTES
            | ENT_SUBSTITUTE,
            'UTF-8'
        );

        $environment = htmlspecialchars(
            $framework->environment(),
            ENT_QUOTES
            | ENT_SUBSTITUTE,
            'UTF-8'
        );

        $phpVersion = htmlspecialchars(
            PHP_VERSION,
            ENT_QUOTES
            | ENT_SUBSTITUTE,
            'UTF-8'
        );

        http_response_code(200);

        header(
            'Content-Type: text/html; charset=UTF-8'
        );

        echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <meta
        name="description"
        content="A modular web application built with IviPHP."
    >
    <title>{$applicationName}</title>

    <style>
        :root {
            color-scheme: light;
            font-family:
                Inter,
                ui-sans-serif,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
            background: #f7f7f5;
            color: #1f1f1f;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 32px;
        }

        main {
            width: min(680px, 100%);
            padding: 56px;
            border: 1px solid #e4e4df;
            border-radius: 24px;
            background: #ffffff;
            box-shadow:
                0 24px 70px
                rgba(31, 31, 31, 0.08);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px;
            border-radius: 999px;
            background: #fff4eb;
            color: #b64e00;
            font-size: 14px;
            font-weight: 700;
        }

        .badge::before {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #f37726;
            content: "";
        }

        h1 {
            margin: 24px 0 12px;
            font-size: clamp(
                42px,
                8vw,
                72px
            );
            line-height: 0.95;
            letter-spacing: -0.06em;
        }

        p {
            margin: 0;
            color: #62625d;
            font-size: 18px;
            line-height: 1.7;
        }

        dl {
            margin: 36px 0 0;
            display: grid;
            grid-template-columns:
                repeat(
                    3,
                    minmax(0, 1fr)
                );
            gap: 12px;
        }

        dl div {
            padding: 18px;
            border: 1px solid #ecece7;
            border-radius: 16px;
            background: #fafaf8;
        }

        dt {
            color: #85857f;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        dd {
            margin: 8px 0 0;
            font-size: 15px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        a {
            color: #d95f0b;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        footer {
            margin-top: 32px;
            color: #85857f;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            main {
                padding: 32px 24px;
            }

            dl {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <main>
        <span class="badge">IviPHP application</span>

        <h1>{$applicationName}</h1>

        <p>
            Your modular PHP web application is running successfully.
            Start building from
            <code>src/</code>
            and configure the application from
            <code>config/</code>.
        </p>

        <dl>
            <div>
                <dt>Version</dt>
                <dd>{$applicationVersion}</dd>
            </div>

            <div>
                <dt>Environment</dt>
                <dd>{$environment}</dd>
            </div>

            <div>
                <dt>PHP</dt>
                <dd>{$phpVersion}</dd>
            </div>
        </dl>

        <footer>
            Health endpoint:
            <a href="/health">/health</a>
        </footer>
    </main>
</body>
</html>
HTML;

        exit;
    }

    http_response_code(404);

    header(
        'Content-Type: application/json; charset=UTF-8'
    );

    echo json_encode(
        [
            'error' => 'Not Found',
            'status' => 404,
            'method' => $method,
            'path' => $path,
        ],
        JSON_PRETTY_PRINT
        | JSON_UNESCAPED_SLASHES
        | JSON_THROW_ON_ERROR
    );
} catch (\Throwable $exception) {
    $debug = filter_var(
        getenv('APP_DEBUG') ?: false,
        FILTER_VALIDATE_BOOLEAN
    );

    http_response_code(500);

    header(
        'Content-Type: application/json; charset=UTF-8'
    );

    $response = [
        'error' => 'Internal Server Error',
        'status' => 500,
    ];

    if ($debug) {
        $response['message'] = $exception
            ->getMessage();

        $response['exception'] = $exception::class;

        $response['file'] = $exception
            ->getFile();

        $response['line'] = $exception
            ->getLine();

        $response['trace'] = explode(
            PHP_EOL,
            $exception->getTraceAsString()
        );
    }

    echo json_encode(
        $response,
        JSON_PRETTY_PRINT
        | JSON_UNESCAPED_SLASHES
        | JSON_THROW_ON_ERROR
    );
}
