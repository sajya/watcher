<?php

declare(strict_types=1);

namespace Sajya\Server\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Response;
use React\Http\Server as HttpServer;
use React\Socket\SecureServer;
use React\Socket\Server as SocketServer;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

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
     * @var Kernel
     */
    private $kernel;

    /**
     * @var HttpFoundationFactory
     */
    private $httpFoundation;

    /**
     * SecureServerCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->loop = Factory::create();
        $this->kernel = app(Kernel::class);
        $this->httpFoundation = new HttpFoundationFactory();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $server = new HttpServer([
            $this->laravelHandle(),
        ]);

        $secure = $this->buildSecureServer();

        $server->listen($secure);

        $this->info('Listening on ' . str_replace('tls:', 'https:', $secure->getAddress()));

        $this->loop->run();
    }

    /**
     * @return SecureServer
     */
    protected function buildSecureServer(): SecureServer
    {
        $socket = new SocketServer('0.0.0.0:0', $this->loop);

        return new SecureServer($socket, $this->loop, [
            'local_cert' => __DIR__ . '/localhost.pem',
        ]);
    }

    /**
     * @return \Closure
     */
    protected function laravelHandle(): callable
    {
        return function (ServerRequestInterface $request) {

            $symfonyRequest = $this->httpFoundation->createRequest($request);
            $response = $this->kernel->handle(Request::createFromBase($symfonyRequest));

            return new Response(
                $response->getStatusCode(),
                $response->headers->all(),
                $response->getContent(),
                $response->getProtocolVersion()
            );
        };
    }
}