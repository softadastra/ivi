<?php

declare(strict_types=1);

/**
 *
 * @file AppServiceProvider.php
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

namespace App\Providers;

use Ivi\Framework\Contracts\ApplicationInterface;
use Ivi\Framework\Providers\ServiceProvider;

/**
 * @class AppServiceProvider
 *
 * @brief Registers application-specific services and configuration.
 *
 * AppServiceProvider defines the core services required by the default
 * IviPHP application skeleton.
 *
 * @since 0.1.0
 */
final class AppServiceProvider extends ServiceProvider
{
    /**
     * @param ApplicationInterface $application
     */
    public function __construct(
        ApplicationInterface $application
    ) {
        parent::__construct($application);
    }

    /**
     * @brief Register application services.
     *
     * Application configuration and paths remain available through the
     * framework application instead of being stored as scalar container
     * instances.
     *
     * @return void
     */
    protected function registerServices(): void
    {
    }

    /**
     * @brief Complete application initialization.
     *
     * @return void
     */
    protected function bootServices(): void
    {
        $timezone = trim(
            (string) $this->config()->get(
                'app.timezone',
                'UTC'
            )
        );

        if (
            $timezone === ''
            || !in_array(
                $timezone,
                timezone_identifiers_list(),
                true
            )
        ) {
            $timezone = 'UTC';
        }

        date_default_timezone_set($timezone);

        $this->prepareDirectory(
            $this->application()->path('storage')
        );

        $this->prepareDirectory(
            $this->application()->path(
                'storage/cache'
            )
        );

        $this->prepareDirectory(
            $this->application()->path(
                'storage/logs'
            )
        );

        $this->prepareDirectory(
            $this->application()->path(
                'storage/views'
            )
        );
    }

    /**
     * @param string $path
     *
     * @return void
     */
    private function prepareDirectory(
        string $path
    ): void {
        if (is_dir($path)) {
            return;
        }

        if (
            !mkdir(
                $path,
                0775,
                true
            )
            && !is_dir($path)
        ) {
            throw new \RuntimeException(
                "Unable to create application directory: {$path}"
            );
        }
    }
}
