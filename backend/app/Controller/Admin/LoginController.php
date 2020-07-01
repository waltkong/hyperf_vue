<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Constants\ErrorCode;
use App\Controller\BaseController;
use App\Logics\Admin\LoginLogic;
use App\Logics\Common\ResponseLogic;
use Hyperf\Di\Annotation\Inject;
use Qbhy\HyperfAuth\Annotation\Auth;
use App\Middleware\OperateLogMiddleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\Middleware;
use App\Exception\AdminResponseException;

class LoginController extends BaseController {


    public function __construct( LoginLogic $loginLogic)
    {
        parent::__construct();

        $this->logic = $loginLogic;

    }

    /**
     * 登录
     */
    public function login(){
        $input = $this->request->all();

        $validator = $this->validationFactory->make($input,[
            'mobile' => 'required|between:2,20',
            'password' => 'required|between:2,20',
        ],[
            'mobile.required' => '手机号不正确',
            'password.required' => '密码不正确',
        ]);
        if ($validator->fails()){
            throw new AdminResponseException(ErrorCode::ERROR,$validator->errors()->first());
        }

        $result = $this->logic->login($input);

        return $this->response->json(ResponseLogic::successData($result));
    }

    /**
     *注册
     */
    public function register(){
        $input = $this->request->all();

        $validator = $this->validationFactory->make($input,[
            'nickname' => 'required|between:2,20',
            'password' => 'required|between:2,20',
            'mobile' => 'required|between:11,11',
            'code' => 'required|between:2,20'
        ],[
            'nickname.required' => '昵称不正确',
            'password.required' => '密码不正确',
            'mobile.required' => '手机不正确',
            'code.required' => '验证码不正确',
        ]);
        if ($validator->fails()){
            throw new AdminResponseException(ErrorCode::ERROR,$validator->errors()->first());
        }

        $result = $this->logic->register($input);

        return $this->response->json(ResponseLogic::successData($result));
    }

    /**
     * 注销
     * 使用 Auth 注解可以保证该方法必须通过某个 guard 的授权，支持同时传多个 guard，不传参数使用默认 guard
     * @Auth("jwt")
     */
    public function logout(){
        $input = $this->request->all();
        auth()->guard('jwt')->logout();
        return $this->response->json(ResponseLogic::successData([]));
    }

}