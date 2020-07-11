<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\ErrorCode;

use App\Model\System\LogModel;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * admin用户操作日志
 * Class OperateLogMiddleware
 * @package App\Middleware
 */
class OperateLogMiddleware implements MiddlewareInterface
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
        try{
            $userRow = auth()->guard('jwt')->user();

            $url = $this->request->url();

            $param = json_encode($this->request->all());

            $servers = $this->request->getServerParams();

            $ip = $this->request->getHeader('x-real-ip');

            LogModel::query()->insertGetId([
                "url" => $url,
                "param" => substr($param,0,255),
                "user_id" => $userRow->id,
                "company_id" => $userRow->company_id,
                "ip" => isset($servers['remote_addr']) && !empty($servers['remote_addr']) ? $servers['remote_addr'] : $ip,
            ]);

        }catch (\Exception $e){
            return $this->response->json(
                [
                    'code' => ErrorCode::BUSINESS_ERROR,
                    'msg' => "记录操作日志出错",
                    'data' => []
                ]
            );
        }
        return $handler->handle($request);
    }


}