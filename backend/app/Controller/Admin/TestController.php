<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Constants\ErrorCode;
use App\Controller\BaseController;
use App\Logics\Admin\CompanyLogic;
use App\Logics\Common\ResponseLogic;
use App\Middleware\OperateLogMiddleware;
use Hyperf\DbConnection\Db;
use Qbhy\HyperfAuth\Annotation\Auth;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\Middleware;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use App\Services\Queue\ExampleQueue;
use App\Exception\AdminResponseException;

/**
 * @AutoController
 */
class TestController extends BaseController{

    private $companylogic;
    public function __construct(CompanyLogic $companyLogic)
    {
        $this->companylogic = $companyLogic;
    }

    /**
     * @Inject
     * @var ExampleQueue
     */
    protected $service;

    /**
     * 传统模式投递消息
     */
    public function queue()
    {

        $this->service->push([
            'group@hyperf.io',
            'https://doc.hyperf.io',
            'send_time' => date('Y-m-d H:i:s',time()),
        ],13);

        return 'success';
    }

    /**
     *
     * 并发测试
     */
    public function coflow(){
        $a = $this->companylogic->dataList([]);
        $b = $this->companylogic->getOne(['id'=>1]);
        return "success";
    }




}