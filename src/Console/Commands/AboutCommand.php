<?php

declare(strict_types=1);

/**
 *
 * @file AboutCommand.php
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

namespace App\Console\Commands;

use Ivi\Console\Contracts\CommandInterface;
use Ivi\Console\Contracts\InputInterface;
use Ivi\Console\Contracts\OutputInterface;

/**
 * @class AboutCommand
 *
 * @brief Displays information about the IviPHP application.
 *
 * @since 0.1.0
 */
final class AboutCommand implements CommandInterface
{
    /**
     * @brief Return the command name.
     *
     * @return string
     */
    public function name(): string
    {
        return 'about';
    }

    /**
     * @brief Return the command description.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Display information about the application and runtime.';
    }

    /**
     * @brief Return command aliases.
     *
     * @return array<int, string>
     */
    public function aliases(): array
    {
        return [
            'info',
        ];
    }

    /**
     * @brief Return the command usage expression.
     *
     * @return string
     */
    public function usage(): string
    {
        return 'about';
    }

    /**
     * @brief Determine whether the command is hidden.
     *
     * @return bool
     */
    public function isHidden(): bool
    {
        return false;
    }

    /**
     * @brief Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $output->writeln('IviPHP');
        $output->writeln(
            'A modular PHP framework for web and console applications.'
        );

        $output->writeln();
        $output->writeln('Application:');
        $output->writeln('  Name: Ivi Application');
        $output->writeln('  Version: 0.1.0');
        $output->writeln(
            '  Environment: '
            . $this->environment()
        );

        $output->writeln();
        $output->writeln('Runtime:');
        $output->writeln(
            '  PHP: ' . PHP_VERSION
        );

        $output->writeln(
            '  SAPI: ' . PHP_SAPI
        );

        $output->writeln(
            '  Operating system: '
            . PHP_OS_FAMILY
        );

        return 0;
    }

    /**
     * @return string
     */
    private function environment(): string
    {
        $environment = getenv('APP_ENV');

        if (
            $environment === false
            || trim($environment) === ''
        ) {
            return 'production';
        }

        return trim($environment);
    }
}
