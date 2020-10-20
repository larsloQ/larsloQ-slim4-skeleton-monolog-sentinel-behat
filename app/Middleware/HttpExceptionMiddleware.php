<?php

/**
 * This middleware does not "alter" the response object,
 * when added to app it will check the response status code and
 * - if not 200 - make a log entry 
 */

declare (strict_types = 1);
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;


final class HttpExceptionMiddleware implements MiddlewareInterface
{

    /*
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;

    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface{

        /* 
         * call handle first, i.e. run this middleware AFTER app has done its thing
         */
        $response   = $handler->handle($request);
        $statusCode = $response->getStatusCode();
        $phrase     = $response->getBody(); //->getContents();
        if ($statusCode !== 200) {
            $this->logger->error("HTTP-ERROR [$statusCode] : $phrase");
        }
        return $response;
    }
}
