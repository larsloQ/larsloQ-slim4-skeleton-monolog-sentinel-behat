<?php
// SentinelAuthCheckMiddleWare.php
/*
 *  SECURITY !!!!!!!!
 *  middleware to secure 'backend' routes, checks for valid user
 * sentinel auth require auth for all routes expect 'hi','auth','texts'
 *
 *
 * https://stackoverflow.com/questions/15734031/why-does-the-preflight-options-request-of-an-authenticated-cors-request-work-in
 * The W3 spec for CORS preflight requests clearly states that user credentials should be excluded. There is a bug in Chrome and WebKit where OPTIONS requests returning a status of 401 still send the subsequent request.
 * How can I get the OPTIONS request to send and respond consistently?
 * Simply have the server (API in this example) respond to OPTIONS requests without requiring authentication.
 */
declare(strict_types=1);
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Psr\Http\Server\MiddlewareInterface;
use Cartalyst\Sentinel\Sentinel;

class SentinelAuthCheckMiddleWare implements MiddlewareInterface
{
    private $sentinel;
    private $basePath;

    public function __construct(Sentinel $sentinel, String $basePath)
    {
        $this->sentinel = $sentinel;
        $this->basePath = $basePath;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /* SLIM 4:
         * How to Short Circuit
         * You just return the response early without calling $handler->handle($request). Essentially $handler->handle($request) is the equivalent of $next($request, $response); (https://github.com/slimphp/Slim/issues/2667)
         */
        $method        = $request->getMethod();
        /* allow options requests */
        if (strtolower($method) !== "options") {
            /*allow 'public routes' */
                $user = $this->sentinel->check();
                if (!$user) {
                    $response = new Response();
                    $response = $response->withStatus(403);
                    $body     = $response->getBody()->write("You need to log in ");
                    return $response;
                }
        }
        /* proceed when having a valid user, calling handler is similar than calling next in Slim V3 */
        $response = $handler->handle($request);
        return $response;
    }
}
