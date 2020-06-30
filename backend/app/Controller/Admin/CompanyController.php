<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Constants\ErrorCode;
use App\Controller\BaseController;
use App\Logics\Admin\CompanyLogic;
use App\Logics\Common\ResponseLogic;
use App\Middleware\OperateLogMiddleware;
use Qbhy\HyperfAuth\Annotation\Auth;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\Middleware;

class CompanyController extends BaseController
{

    public function __construct(CompanyLogic $companyLogic)
    {
        parent::__construct();
        $this->logic = $companyLogic;

        $input = $this->request->all();
        return $this->response->json($input);
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

        return $this->response->json(ResponseLogic::successData());
    }


    public function test1()
    {
        $input = $this->request->all();
        return $this->response->json($input);
    }


    /**
     * 查一个
     * @Auth("jwt")
     * @Middlewares({
     *     @Middleware(OperateLogMiddleware::class)
     * })
     */
    public function getOne()
    {
        $input = $this->request->all();

//        return $this->response->raw(\GuzzleHttp\json_encode($input)) ;
//
//        $this->validateLogic->commonAdminValidate($input,
//            [
//                'id' => 'required|numeric',
//            ],
//            [
//                'id.required' => 'id必要',
//            ]
//        );

        $result = $this->logic->getOne($input);

//        return $this->response->json(ResponseLogic::successData($result));

        return $this->response->json($input);
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

