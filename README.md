# Ivi

The official application skeleton for building modular web and console applications with IviPHP.

`iviphp/ivi` provides a minimal project structure powered by `iviphp/framework`. It includes application bootstrapping, configuration, service providers, console commands and writable storage directories without imposing a large application architecture.

## Requirements

- PHP 8.2 or later
- Composer
- Required PHP extensions for the database driver used by the application

## Installation

Create a new IviPHP application:

```bash
composer create-project iviphp/ivi my-application
```

Enter the project directory:

```bash
cd my-application
```

Create the local environment file:

```bash
cp .env.example .env
```

Make the console executable:

```bash
chmod +x bin/console
```

Display the available commands:

```bash
php bin/console
```

or:

```bash
./bin/console
```

## Project structure

```text
.
├── bin/
│   └── console
├── bootstrap/
│   └── app.php
├── config/
│   ├── app.php
│   └── database.php
├── public/
│   └── index.php
├── src/
│   ├── Console/
│   │   └── Commands/
│   │       └── AboutCommand.php
│   ├── Http/
│   │   └── Controllers/
│   └── Providers/
│       └── AppServiceProvider.php
├── storage/
│   ├── cache/
│   ├── logs/
│   └── views/
├── .env.example
├── composer.json
├── LICENSE
└── README.md
```

## Application bootstrap

The application is created in:

```text
bootstrap/app.php
```

This file:

- loads Composer;
- reads the local `.env` file;
- loads application configuration;
- creates the framework instance;
- registers application service providers;
- registers console commands;
- returns the configured framework.

Example:

```php
<?php

declare(strict_types=1);

use App\Console\Commands\AboutCommand;
use App\Providers\AppServiceProvider;
use Ivi\Config\Config;
use Ivi\Framework\Framework;

$basePath = dirname(__DIR__);

require_once $basePath
    . '/vendor/autoload.php';

$config = Config::load(
    $basePath . '/config'
);

$framework = Framework::create(
    basePath: $basePath,
    config: $config,
    environment: (string) $config->get(
        'app.environment',
        'production'
    ),
    consoleName: (string) $config->get(
        'app.name',
        'Ivi Application'
    ),
    consoleVersion: (string) $config->get(
        'app.version',
        '0.1.0'
    )
);

$framework->provider(
    AppServiceProvider::class
);

$framework->registerCommand(
    new AboutCommand()
);

return $framework;
```

The bootstrap file may be reused by console and web entry points.

## Environment configuration

Create `.env` from the example file:

```bash
cp .env.example .env
```

Default values:

```dotenv
APP_NAME="Ivi Application"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_DRIVER=sqlite
DB_DATABASE=storage/database.sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=ivi
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=file
CACHE_PATH=storage/cache

LOG_CHANNEL=file
LOG_LEVEL=debug
LOG_PATH=storage/logs/app.log
```

The `.env` file is ignored by Git and should contain machine-specific or private configuration.

Do not commit production credentials.

## Application configuration

Application configuration is stored in:

```text
config/app.php
```

Available settings include:

```php
return [
    'name' => 'Ivi Application',
    'version' => '0.1.0',
    'environment' => 'production',
    'debug' => false,
    'url' => 'http://localhost:8000',
    'timezone' => 'UTC',
    'locale' => 'en',
];
```

Retrieve configuration through the framework:

```php
$name = $framework
    ->config()
    ->get(
        'app.name',
        'Ivi Application'
    );
```

Check the current environment:

```php
if ($framework->isEnvironment('production')) {
    // Production behavior.
}
```

Check several environments:

```php
if (
    $framework->isEnvironment(
        'development',
        'testing'
    )
) {
    // Development or testing behavior.
}
```

## Database configuration

Database configuration is stored in:

```text
config/database.php
```

The default driver is SQLite:

```dotenv
DB_DRIVER=sqlite
DB_DATABASE=storage/database.sqlite
```

Create the SQLite database file:

```bash
touch storage/database.sqlite
```

### MySQL

```dotenv
DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=ivi
DB_USERNAME=root
DB_PASSWORD=
```

### PostgreSQL

```dotenv
DB_DRIVER=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_NAME=ivi
DB_USERNAME=postgres
DB_PASSWORD=
```

The selected database package or provider is responsible for consuming this configuration and establishing connections.

## Service providers

Application services are registered through service providers.

The default provider is:

```text
src/Providers/AppServiceProvider.php
```

It registers application metadata and common paths in the service container.

Available services include:

```text
app.name
app.version
app.environment
app.debug
app.url

path.base
path.bootstrap
path.config
path.public
path.storage
path.cache
path.logs
path.views
```

Resolve a service:

```php
$name = $framework->make('app.name');

$storagePath = $framework->make(
    'path.storage'
);
```

Check whether a service exists:

```php
if ($framework->has('path.logs')) {
    $logs = $framework->make(
        'path.logs'
    );
}
```

## Creating a service provider

Create a provider in:

```text
src/Providers
```

Example:

```php
<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Mailer;
use Ivi\Framework\Contracts\ApplicationInterface;
use Ivi\Framework\Providers\ServiceProvider;

final class MailServiceProvider extends ServiceProvider
{
    public function __construct(
        ApplicationInterface $application
    ) {
        parent::__construct(
            $application,
            [
                Mailer::class,
                'mailer',
            ]
        );
    }

    protected function registerServices(): void
    {
        $mailer = new Mailer();

        $this->container()->instance(
            Mailer::class,
            $mailer
        );

        $this->container()->instance(
            'mailer',
            $mailer
        );
    }

    protected function bootServices(): void
    {
        $mailer = $this->make(
            Mailer::class
        );

        $mailer->initialize();
    }
}
```

Register it in `bootstrap/app.php`:

```php
use App\Providers\MailServiceProvider;

$framework->provider(
    MailServiceProvider::class
);
```

Register several providers:

```php
$framework->providers([
    App\Providers\DatabaseServiceProvider::class,
    App\Providers\MailServiceProvider::class,
]);
```

## Provider lifecycle

Service providers have two lifecycle stages.

### Registration

```php
protected function registerServices(): void
{
    // Register container services.
}
```

Registration happens when the provider is added to the framework.

Use this stage for:

- service instances;
- factories;
- aliases;
- configuration defaults;
- dependency bindings.

### Booting

```php
protected function bootServices(): void
{
    // Complete application initialization.
}
```

Booting happens after application bootstrap completes.

Use this stage for:

- resolving services;
- initializing components;
- registering listeners;
- preparing directories;
- connecting framework modules.

## Console commands

Console commands are stored in:

```text
src/Console/Commands
```

The default application contains:

```text
AboutCommand
```

Run it with:

```bash
php bin/console about
```

or through its alias:

```bash
php bin/console info
```

Example output:

```text
IviPHP
A modular PHP framework for web and console applications.

Application:
  Name: Ivi Application
  Version: 0.1.0
  Environment: development

Runtime:
  PHP: 8.2.x
  SAPI: cli
  Operating system: Linux
```

## Creating a command

Create a command class:

```php
<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Ivi\Console\Contracts\CommandInterface;
use Ivi\Console\Contracts\InputInterface;
use Ivi\Console\Contracts\OutputInterface;

final class HelloCommand implements CommandInterface
{
    public function name(): string
    {
        return 'hello';
    }

    public function description(): string
    {
        return 'Display a greeting.';
    }

    public function aliases(): array
    {
        return [];
    }

    public function usage(): string
    {
        return 'hello [name]';
    }

    public function isHidden(): bool
    {
        return false;
    }

    public function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $name = $input->argument(
            0,
            'Developer'
        );

        $output->success(
            "Hello, {$name}."
        );

        return 0;
    }
}
```

Register it in `bootstrap/app.php`:

```php
use App\Console\Commands\HelloCommand;

$framework->registerCommand(
    new HelloCommand()
);
```

Run it:

```bash
php bin/console hello Gaspard
```

## Closure-backed commands

Commands may also be registered directly:

```php
use Ivi\Console\Contracts\InputInterface;
use Ivi\Console\Contracts\OutputInterface;

$framework->command(
    name: 'app:environment',
    handler: static function (
        InputInterface $input,
        OutputInterface $output
    ) use ($framework): int {
        $output->info(
            'Environment: '
            . $framework->environment()
        );

        return 0;
    },
    description: 'Display the application environment.',
    aliases: [
        'env',
    ]
);
```

Run it:

```bash
php bin/console app:environment
```

or:

```bash
php bin/console env
```

## Console help

Display all commands:

```bash
php bin/console
```

or:

```bash
php bin/console list
```

Display help for one command:

```bash
php bin/console help about
```

Command help is also available with:

```bash
php bin/console about --help
```

Display the application version:

```bash
php bin/console --version
```

## Console entry point

The executable console file is:

```text
bin/console
```

It:

- loads the application bootstrap;
- validates the returned framework instance;
- starts the framework;
- executes the requested command;
- returns the command exit code;
- displays startup failures safely.

Run it through PHP:

```bash
php bin/console
```

Make it directly executable:

```bash
chmod +x bin/console
```

Then run:

```bash
./bin/console
```

## Debug mode

Enable debug mode in `.env`:

```dotenv
APP_DEBUG=true
```

When console startup fails, debug mode displays:

- exception class;
- source file;
- source line;
- stack trace.

Disable it in production:

```dotenv
APP_DEBUG=false
```

## Storage directories

Writable application files are stored in:

```text
storage/
```

The default directories are:

```text
storage/cache
storage/logs
storage/views
```

They are automatically created by Composer and by the default application service provider.

Create them manually when necessary:

```bash
mkdir -p \
  storage/cache \
  storage/logs \
  storage/views
```

The contents of these directories are ignored by Git.

## Resolving application paths

The framework safely resolves paths relative to the project root.

```php
$configPath = $framework->path(
    'config/app.php'
);

$storagePath = $framework->path(
    'storage'
);

$logsPath = $framework->path(
    'storage/logs'
);
```

Absolute paths and parent-directory traversal are rejected.

## Accessing framework services

Retrieve the application:

```php
$application = $framework->application();
```

Retrieve the service container:

```php
$container = $framework->container();
```

Retrieve configuration:

```php
$config = $framework->config();
```

Retrieve the console:

```php
$console = $framework->console();
```

Retrieve the framework manager:

```php
$manager = $framework->manager();
```

## Application startup

Start the application manually:

```php
$framework->start();
```

This performs:

```text
Bootstrap application
Boot service providers
```

The lifecycle is idempotent. Completed stages are not executed again.

Bootstrap without booting providers:

```php
$framework->bootstrap();
```

Boot registered providers:

```php
$framework->boot();
```

Check the lifecycle state:

```php
if ($framework->isBootstrapped()) {
    // Bootstrap operations completed.
}

if ($framework->isBooted()) {
    // Providers completed booting.
}

if ($framework->isStarted()) {
    // Application is fully started.
}
```

## Custom bootstrap operations

Register application initialization logic:

```php
use Ivi\Framework\Contracts\ApplicationInterface;

$framework->bootstrapWith(
    'prepare.uploads',
    static function (
        ApplicationInterface $application
    ): void {
        $path = $application->path(
            'storage/uploads'
        );

        if (!is_dir($path)) {
            mkdir(
                $path,
                0775,
                true
            );
        }
    }
);
```

Bootstrap operations execute in registration order.

## Development server

Once the public HTTP entry point is configured, start PHP's built-in development server:

```bash
php -S 127.0.0.1:8000 -t public
```

Open:

```text
http://127.0.0.1:8000
```

The built-in PHP server is intended for local development only.

## Composer commands

Install dependencies:

```bash
composer install
```

Update dependencies:

```bash
composer update
```

Refresh autoload files:

```bash
composer dump-autoload
```

Validate the project configuration:

```bash
composer validate --strict
```

## Production preparation

Before deploying:

```bash
composer install \
  --no-dev \
  --optimize-autoloader
```

Use production environment settings:

```dotenv
APP_ENV=production
APP_DEBUG=false
```

Ensure the storage directories are writable by the application process:

```bash
chmod -R 775 storage
```

Deployment permissions should be adapted to the server and user configuration.

## Extending the application

Application code belongs under:

```text
src/
```

Recommended namespaces:

```text
App\Console\Commands
App\Http\Controllers
App\Providers
App\Services
App\Repositories
App\Models
```

Composer maps the `App` namespace to `src`:

```json
{
  "autoload": {
    "psr-4": {
      "App\\": "src/"
    }
  }
}
```

After adding or moving classes, regenerate Composer autoload files:

```bash
composer dump-autoload
```

## Design principles

The Ivi application skeleton follows these principles:

- minimal initial structure;
- modular framework packages;
- explicit application bootstrapping;
- service-provider based composition;
- dependency-container integration;
- console-first developer tooling;
- environment-based configuration;
- replaceable application services;
- predictable application lifecycle;
- no unnecessary application abstractions.

## License

Ivi is open-source software released under the MIT License.

## Maintainer

Maintained by [Gaspard Kirira](https://github.com/GaspardKirira) and [Softadastra](https://softadastra.com).
