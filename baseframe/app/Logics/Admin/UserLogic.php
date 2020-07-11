<?php
namespace App\Logics\Admin;

use App\Constants\ErrorCode;
use App\Exception\AdminResponseException;
use App\Logics\Common\DatabaseLogic;
use App\Logics\Common\PageLogic;
use App\Model\User\UserModel;

class UserLogic{

    protected $authLogic;

    private $loginlogic;

    public function __construct(LoginLogic $loginLogic)
    {
        $this->loginlogic = $loginLogic;
    }

    public function dataList($input)
    {
        $qsFunc = function () use($input){
            $obj = UserModel::query();
            if(isset($input['mobile']) && strlen($input['mobile']) > 0){
                $obj = $obj->where('mobile','like',"%{$input['mobile']}%");
            }
            if(isset($input['nickname']) && strlen($input['nickname']) > 0){
                $obj = $obj->where('nickname','like',"%{$input['nickname']}%");
            }
            if(isset($input['company_id']) && strlen($input['company_id']) > 0){
                $obj = $obj->where('company_id','=',"{$input['company_id']}");
            }
            $obj = PageLogic::startAndEndTimeQuerySetFilter($obj,$input);
            $obj = PageLogic::superCompanyQuerySetFilter($obj,$input);
            return $obj;
        };

        $list = PageLogic::getPaginateListQuerySet($qsFunc(),$input)->get()->toArray();

        foreach ($list as $k => &$item){
            unset($item['salt']);
            unset($item['password']);
        }

        $ret = [
            'data' => $list,
            'count' =>  count($list),
            'total' =>  $qsFunc()->count()
        ];
        return PageLogic::commonListDataReturn($ret);

    }

    public function storeOrUpdate($input)
    {
        $id = $input['id'] ?? '';

        try{
            if(empty($id)){

                if(!isset($input['password']) || empty($input['password'])){
                    throw new \Exception('密码必要');
                }
                //新增变更密码
                $input['salt'] = $this->loginlogic->generateSalt();
                $input['password'] = $this->loginlogic->encryptPassword($input['password'],$input['salt']);

                $findcheck = UserModel::query()
                    ->where('mobile',$input['mobile'])
                    ->where('company_id',$input['company_id'])
                    ->exists();
                if($findcheck){
                    throw new AdminResponseException(ErrorCode::ERROR,"该手机已存在");
                }

                DatabaseLogic::commonInsertData(UserModel::class,$input);

            }else{
                $findcheck = UserModel::query()
                    ->where('mobile',$input['mobile'])
                    ->where('id','<>',$id)
                    ->where('company_id',$input['company_id'])
                    ->exists();
                if($findcheck){
                    throw new AdminResponseException(ErrorCode::ERROR,"该手机已存在");
                }

                //更新密码不变
                unset($input['password']);
                unset($input['salt']);

                $qs = UserModel::query()->where('id',$id);

                DatabaseLogic::commonUpdateData(UserModel::class,$qs,$input);

            }
        }catch (\Exception $e){
            throw new AdminResponseException(ErrorCode::SYSTEM_INNER_ERROR,$e->getMessage());
        }

    }

    public function getOne($input)
    {
        $row = UserModel::query()->where('id',$input['id'])->first();
        return [
            'data' => $row,
        ];
    }


    public function changePassword($input){
        $user = auth()->guard('jwt')->user();
        if($user->id != $input['id']){
            throw new AdminResponseException(ErrorCode::ERROR,'非法操作');
        }
        $row = UserModel::query()->where('id',$input['id'])->first();

        if(is_null($row)){
            throw new AdminResponseException(ErrorCode::ERROR,'用户不存在');
        }
        if($row->password != $this->loginlogic->encryptPassword($input['old_password'],$row->salt)){
            throw new AdminResponseException(ErrorCode::ERROR,'旧密码错误');
        }
        try{
            
            $salt = $this->loginlogic->generateSalt();
            $password = $this->loginlogic->encryptPassword($input['new_password'],$salt);
            $row->salt = $salt;
            $row->password = $password;
            $row->save();

        }catch (\Exception $e){

            throw new AdminResponseException(ErrorCode::SYSTEM_INNER_ERROR,$e->getMessage());

        }
    }

    public function deleteOne($input)
    {
        try{
            UserModel::query()->where('id',$input['id'])->delete();

        }catch (\Exception $e){
            throw new AdminResponseException(ErrorCode::SYSTEM_INNER_ERROR,$e->getMessage());
        }
    }

}