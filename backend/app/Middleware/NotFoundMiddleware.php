<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Constants\ErrorCode;
use App\Exception\AdminResponseException;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Utils\Contracts\Arrayable;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class NotFoundMiddleware extends \Hyperf\HttpServer\CoreMiddleware
{
    public function __construct(ContainerInterface $container, string $serverName='')
    {
        parent::__construct($container, $serverName);
    }

    /**
     * Handle the response when cannot found any routes.
     *
     * @return array|Arrayable|mixed|ResponseInterface|string
     */
    protected function handleNotFound(ServerRequestInterface $request)
    {
        // 重写路由找不到的处理逻辑
//        return $this->response()->withStatus(404);

        throw new AdminResponseException(ErrorCode::PERMISSION_NO_ROUTE);

    }

    /**
     * Handle the response when the routes found but doesn't match any available methods.
     *
     * @return array|Arrayable|mixed|ResponseInterface|string
     */
    protected function handleMethodNotAllowed(array $methods, ServerRequestInterface $request)
    {
        // 重写 HTTP 方法不允许的处理逻辑
//        return $this->response()->withStatus(405);

        throw new AdminResponseException(ErrorCode::PERMISSION_METHOD_NOT_ALLOWED);

    }
}