<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Logics\Admin\UserLogic;
use Hyperf\HttpServer\Contract\RequestInterface;
use App\Controller\BaseController;
use App\Logics\Common\ResponseLogic;
use Qbhy\HyperfAuth\Annotation\Auth;
use App\Middleware\OperateLogMiddleware;
use App\Constants\ErrorCode;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\Middleware;


class UserController extends BaseController
{

    public function __construct(UserLogic $userLogic)
    {
        parent::__construct();
        $this->logic = $userLogic;
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
                'company_id' => 'required|numeric',
                'nickname' => 'required|between:1,255',
                'mobile' => 'required|between:1,255',
                'avatar' => 'required|between:1,255',
                'admin_status' => 'required|numeric',
            ],
            [
                'company_id.required' => '公司必要',
                'nickname.required' => '昵称必要',
                'mobile.required' => '手机必要',
                'avatar.required' => '头像必要',
                'admin_status.required' => '管理员状态必要',
            ]
        );

        $this->logic->storeOrUpdate($input);

        return $this->response->json(ResponseLogic::successData([]));
    }

    /**
     * 修改密码
     * @Auth("jwt")
     * @Middlewares({
     *     @Middleware(OperateLogMiddleware::class)
     * })
     */
    public function changePassword(){
        $input = $this->request->all();

        $this->validateLogic->commonAdminValidate($input,
            [
                'id' => 'required|numeric',
                'old_password' => 'required|between:1,255',
                'new_password' => 'required|between:1,255',
            ],
            [
                'id.required' => 'id必要',
                'old_password.required' => '旧密码必要',
                'new_password.required' => '新密码必要',
            ]
        );

        $this->logic->changePassword($input);

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

