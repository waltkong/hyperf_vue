<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Logics\Admin\LogLogic;
use App\Logics\Common\ResponseLogic;
use Qbhy\HyperfAuth\Annotation\Auth;
use App\Middleware\OperateLogMiddleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\Middleware;
use App\Constants\ErrorCode;


class LogController extends BaseController
{

    public function __construct(LogLogic $logLogic)
    {
        parent::__construct();
        $this->logic = $logLogic;
    }

    /**
     * 列表
     * @Auth("jwt")
     */
    public function dataList()
    {
        $input = $this->request->all();

        $result = $this->logic->dataList($input);

        return $this->response->json(ResponseLogic::successData($result));
    }

    /**
     * 增or改
     * @Auth("jwt")
     * @Middlewares({
     *     @Middleware(OperateLogMiddleware::class)
     * })
     */
    public function storeOrUpdate()
    {
        $input = $this->request->all();

        return $this->response->json(ResponseLogic::successData([]));
    }


    /**
     * 查一个
     * @Auth("jwt")
     */
    public function getOne()
    {
        $input = $this->request->all();

        $this->validateLogic->commonAdminValidate($input,
            [
                'id' => 'required|numeric',
            ],
            [
                'id.required' => 'id必要',
            ]
        );

        return $this->response->json(ResponseLogic::successData([]));
    }

    /**
     * 删除
     * @Auth("jwt")
     * @Middlewares({
     *     @Middleware(OperateLogMiddleware::class)
     * })
     */
    public function deleteOne()
    {
        $input = $this->request->all();

        $this->validateLogic->commonAdminValidate($input,
            [
                'id' => 'required|numeric',
            ],
            [
                'id.required' => 'id必要',
            ]
        );

        return $this->response->json(ResponseLogic::successData([]));
    }


}

