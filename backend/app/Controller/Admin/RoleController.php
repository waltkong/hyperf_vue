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
use App\Exception\AdminResponseException;

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

        $validator = $this->validationFactory->make($input,[
            'name' => 'required|between:1,255',
        ],[
            'name.required' => '名称必要',
        ]);
        if ($validator->fails()){
            throw new AdminResponseException(ErrorCode::ERROR,$validator->errors()->first());
        }

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

        $validator = $this->validationFactory->make($input,[
            'id' => 'required|numeric',
        ],[
            'id.required' => '缺少id',
        ]);
        if ($validator->fails()){
            throw new AdminResponseException(ErrorCode::ERROR,$validator->errors()->first());
        }

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

        $validator = $this->validationFactory->make($input,[
            'id' => 'required|numeric',
        ],[
            'id.required' => '缺少id',
        ]);
        if ($validator->fails()){
            throw new AdminResponseException(ErrorCode::ERROR,$validator->errors()->first());
        }

        return $this->response->json(ResponseLogic::successData([]));
    }

    /**
     * 添加角色时所有菜单
     * @Auth("jwt")
     */
    public function getAllMenus(){
        $input = $this->request->all();

        $result = $this->logic->getAllMenus();

        return $this->response->json(ResponseLogic::successData($result));
    }

    /**
     * 获取指定角色的所有授权菜单
     * @Auth("jwt")
     */
    public function getThisRoleMenus(){
        $input = $this->request->all();

        $validator = $this->validationFactory->make($input,[
            'id' => 'required|numeric',
        ],[
            'id.required' => '缺少id',
        ]);
        if ($validator->fails()){
            throw new AdminResponseException(ErrorCode::ERROR,$validator->errors()->first());
        }

        $result = $this->logic->getThisRoleMenus($input);

        return $this->response->json(ResponseLogic::successData($result));
    }

    /**
     * 获取这个公司所有角色
     * @Auth("jwt")
     */
    public function thisCompanyRoleOptions(){
        $input = $this->request->all();

        $result = $this->logic->thisCompanyRoleOptions();

        return $this->response->json(ResponseLogic::successData($result));
    }


}

