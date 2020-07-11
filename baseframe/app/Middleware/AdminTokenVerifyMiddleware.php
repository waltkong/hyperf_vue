<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\ErrorCode;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * admin用户的token验证
 * Class AdminTokenVerifyMiddleware
 * @package App\Middleware
 */
class AdminTokenVerifyMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var HttpResponse
     */
    protected $response;

    public function __construct(ContainerInterface $container, HttpResponse $response, RequestInterface $request)
    {
        $this->container = $container;
        $this->response = $response;
        $this->request = $request;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $tokencheck = auth()->guard('jwt')->check();

        if(!$tokencheck){
            return $this->response->json(
                [
                    'code' => ErrorCode::USER_TOKEN_ERROR,
                    'msg' => 'token无效',
                    'data' => []
                ]
            );
        }

        return $handler->handle($request);
    }
}