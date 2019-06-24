<?php

declare(strict_types=1);

namespace Sajya\Server;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;

class FileHandle
{

    /**
     * @param ServerRequestInterface $request
     * @param callable               $next
     *
     * @return Response
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $file = public_path($request->getUri()->getPath());

        if (!is_file($file)) {
            return $next($request);
        }

        return new Response(200, [], file_get_contents($file));
    }
}