<?php
namespace App\Logics\Admin;

use App\Constants\ErrorCode;
use App\Exception\AdminResponseException;
use App\Model\User\UserModel;
use Hyperf\Di\Annotation\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;
use App\Event\UserRegistered;

class LoginLogic
{

    /**
     * @Inject
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct()
    {

    }


    public function register(array $input)
    {
        $user_exist = UserModel::query()->where('mobile', $input['mobile'])->exists();
        if($user_exist){
            throw new AdminResponseException(ErrorCode::USER_HAS_EXIST);
        }
        $salt = $this->generateSalt();

        $userModel = new UserModel();
        $userModel->mobile = $input['mobile'];
        $userModel->salt = $salt;
        $userModel->nickname = $input['nickname'];
        $userModel->password = $this->encryptPassword($input['password'], $salt);
        $userModel->avatar = $salt;  //todo
        $result = $userModel->save();

        $token = auth()->guard('jwt')->login($userModel);

        // 这里 dispatch(object $event) 会逐个运行监听该事件的监听器
        $this->eventDispatcher->dispatch(new UserRegistered($userModel));

        return [
            'token' => $token,
            'user' => [
                'id' => $userModel->id,
                'mobile' => $userModel->mobile,
                'nickname' => $userModel->nickname,
                'avatar' => $userModel->avatar,
            ],
        ];

    }

    public function login(array $input)
    {
        $userModel = UserModel::query()->where('mobile', $input['mobile'])->first();

        if(is_null($userModel)){
            throw new AdminResponseException(ErrorCode::USER_ACCOUNT_ERROR);
        }

        if($this->encryptPassword($input['password'], $userModel->salt) != $userModel->password ){
            throw new AdminResponseException(ErrorCode::USER_ACCOUNT_ERROR);
        }

        $token = auth()->guard('jwt')->login($userModel);

        return [
            'token' => $token,
            'user' => [
                'id' => $userModel->id,
                'mobile' => $userModel->mobile,
                'nickname' => $userModel->nickname,
                'avatar' => $userModel->avatar,
            ],
        ];

    }

    public function getAuthUser()
    {
        $user = auth()->guard('jwt')->user();
        return $user;
    }


    public function generateSalt()
    {
        return mt_rand(100000,999999);
    }


    public function encryptPassword($pass, $salt)
    {
        return strtolower(md5(md5($pass).$salt));
    }



}