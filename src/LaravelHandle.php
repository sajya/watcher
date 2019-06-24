<?php

declare(strict_types=1);

namespace Sajya\Server;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

class LaravelHandle
{
    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var HttpFoundationFactory
     */
    private $httpFoundation;

    /**
     * laravelHandle constructor.
     */
    public function __construct()
    {
        $this->kernel = app(Kernel::class);
        $this->httpFoundation = new HttpFoundationFactory();
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return Response
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $symfonyRequest = $this->httpFoundation->createRequest($request);

        $response = $this->kernel->handle(Request::createFromBase($symfonyRequest));

        return new Response(
            $response->getStatusCode(),
            $response->headers->all(),
            $response->getContent(),
            $response->getProtocolVersion()
        );
    }
}