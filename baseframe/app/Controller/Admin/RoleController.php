<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Logics\Admin\RoleLogic;
use App\Logics\Common\ResponseLogic;
use Qbhy\HyperfAuth\Annotation\Auth;
use App\Middleware\OperateLogMiddleware;
use App\Constants\ErrorCode;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\Middleware;

class RoleController extends BaseController
{

    public function __construct(RoleLogic $roleLogic)
    {
        parent::__construct();
        $this->logic = $roleLogic;
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

        $this->validateLogic->commonAdminValidate($input,
            [
                'name' => 'required|between:1,255',
            ],
            [
                'name.required' => '名称必要',
            ]
        );

        $this->logic->storeOrUpdate($input);

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

        $result = $this->logic->getOne($input);

        return $this->response->json(ResponseLogic::successData($result));
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

