<?php

declare(strict_types=1);

namespace Sajya\Server;

use Illuminate\Support\ServiceProvider;
use Sajya\Server\Commands\CertificateGenerationCommand;
use Sajya\Server\Commands\SecureServerCommand;

class ServerServiceProvider extends ServiceProvider
{
    /**
     * The available command shortname.
     *
     * @var array
     */
    protected $commands = [
        CertificateGenerationCommand::class,
        SecureServerCommand::class,
    ];

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->publishes([__DIR__.'/../config/server.php' => config_path('server.php'),], 'config');
        $this->mergeConfigFrom(__DIR__.'/../config/server.php', 'server');
    }

    /**
     * Register bindings the service provider.
     */
    public function register(): void
    {
        $this->commands($this->commands);
    }
}