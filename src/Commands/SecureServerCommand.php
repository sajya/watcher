<?php

declare(strict_types=1);

namespace Sajya\Server\Commands;

use Illuminate\Console\Command;
use React\EventLoop\Factory;
use React\Http\Middleware\LimitConcurrentRequestsMiddleware;
use React\Http\Middleware\RequestBodyBufferMiddleware;
use React\Http\Middleware\RequestBodyParserMiddleware;
use React\Http\Server as HttpServer;
use React\Http\StreamingServer;
use React\Socket\SecureServer;
use React\Socket\Server as SocketServer;
use Sajya\Server\FileHandle;
use Sajya\Server\LaravelHandle;

class SecureServerCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'sajya:server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run local web server';

    /**
     * @var \React\EventLoop\LoopInterface
     */
    private $loop;

    /**
     * SecureServerCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->loop = Factory::create();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $server = new StreamingServer([
            new LimitConcurrentRequestsMiddleware(100), // 100 concurrent buffering handlers, queue otherwise
            new RequestBodyBufferMiddleware(8 * 1024 * 1024), // 8 MiB max, ignore body otherwise
            new RequestBodyParserMiddleware(100 * 1024, 1), // 1 file with 100 KiB max, reject upload otherwise
            new FileHandle(),
            new LaravelHandle(),
        ]);

        $secure = $this->buildSecureServer();

        $server->listen($secure);

        $server->on('error', function (Throwable $e) {
            echo 'Error: ' . $e->getMessage() . PHP_EOL;
        });

        $this->info('Listening on ' . str_replace('tls:', 'https:', $secure->getAddress()));

        $this->loop->run();
    }

    /**
     * @return SecureServer
     */
    protected function buildSecureServer(): SecureServer
    {
        $socket = new SocketServer(config('server.uri'), $this->loop);

        return new SecureServer($socket, $this->loop, [
            'local_cert' => storage_path('app/sajya.pem'),
        ]);
    }
}