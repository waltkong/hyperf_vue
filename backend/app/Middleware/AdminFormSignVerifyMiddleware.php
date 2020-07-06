<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\AdminCommonConstant;
use App\Constants\ErrorCode;
use App\Tools\Encrypt\FormSignTool;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 后台请求签名校验
 * Class AdminFormSignVerifyMiddleware
 * @package App\Middleware
 */
class AdminFormSignVerifyMiddleware implements MiddlewareInterface
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
        $signKey = AdminCommonConstant::FORM_SIGN_API_KEY;

        try{
            $input = $this->request->all();
            $signFields = $input[AdminCommonConstant::FORM_SIGN_FIELDS] ?? '';  //前端规定需要延签的所有字段，逗号分隔
            if(!empty($signFields)){
                if(!isset($input[AdminCommonConstant::FORM_SIGN])){
                    throw new \Exception('缺失sign值');
                }
                $signFieldsArray = explode(',',$signFields);
                $signDatas = [];
                foreach ($signFieldsArray as  $item){
                    if(!isset($input[$item])){
                        throw new \Exception('缺失检验字段');
                    }
                    $signDatas[$item] = $input[$item];
                }
                $res = FormSignTool::checkSign($signKey,$signDatas,$input[AdminCommonConstant::FORM_SIGN]);
                if(!$res){
                    throw new \Exception('签名出错');
                }
            }

        }catch (\Exception $e){
            return $this->response->json(
                [
                    'code' => ErrorCode::PARAMS_SIGN_ERROR,
                    'msg' => $e->getMessage(),
                    'data' => []
                ]
            );
        }
        return $handler->handle($request);
    }


}