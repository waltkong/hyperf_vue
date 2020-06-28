<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;

use App\Logics\Admin\ConfigLogic;
use App\Logics\Common\ResponseLogic;
use Qbhy\HyperfAuth\Annotation\Auth;
use App\Middleware\OperateLogMiddleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\Middleware;
use App\Constants\ErrorCode;

class ConfigController extends BaseController
{

    public function __construct(ConfigLogic $configLogic)
    {
        parent::__construct();
        $this->logic = $configLogic;
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
                'group_name' => 'required|between:1,255',
                'type' => 'required',
            ],
            [
                'name.required' => '名称必要',
                'group_name.required' => '组别必要',
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

        $this->logic->deleteOne($input);

        return $this->response->json(ResponseLogic::successData([]));
    }


}

