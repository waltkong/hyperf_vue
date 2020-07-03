<?php
namespace App\Logics\Admin;

use App\Constants\ErrorCode;
use App\Exception\AdminResponseException;
use App\Logics\Common\DatabaseLogic;
use App\Logics\Common\PageLogic;
use App\Model\User\CompanyModel;
use App\Model\User\Role2UserModel;
use App\Model\User\RoleModel;
use App\Model\User\UserModel;
use Hyperf\DbConnection\Db;

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
            $obj = PageLogic::attachCompanyQuerySetFilter($obj,$input);
            return $obj;
        };

        $collection = PageLogic::getPaginateListQuerySet($qsFunc(),$input)->get();

        foreach ($collection as $k => $item){
            $company = $item->its_company;
            $role = $item->its_role;
            unset($item->salt);
            unset($item->password);
            if(!is_null($company)){
                $collection[$k]->company_name = $company->name;
            }

            if(!is_null($role)){
                $roleNames = array_column($role->toArray(),'name');
                $collection[$k]->role_name = explode(',',$roleNames);
            }
        }

        $ret = [
            'data' => $collection,
            'count' =>  count($collection),
            'total' =>  $qsFunc()->count()
        ];
        return PageLogic::commonListDataReturn($ret);

    }

    public function storeOrUpdate($input)
    {
        $id = $input['id'] ?? '';

        Db::beginTransaction();
        try{
            if(empty($id)){
                $this->store($input);
            }else{
                $this->update($input);
            }
            Db::commit();
        }catch (\Exception $e){
            Db::rollBack();
            throw new AdminResponseException(ErrorCode::SYSTEM_INNER_ERROR,$e->getMessage());
        }

    }


    public function update($input)
    {
        $user = auth()->guard('jwt')->user();
        $findcheck = UserModel::query()
            ->where('mobile',$input['mobile'])
            ->where('id','<>',$input['id'])
            ->where('company_id',$input['company_id'])
            ->exists();
        if($findcheck){
            throw new AdminResponseException(ErrorCode::ERROR,"该手机已存在");
        }
        //更新密码不变
        unset($input['password']);
        unset($input['salt']);
        $data = DatabaseLogic::filterTableData(UserModel::FIELDS,$input);
        UserModel::query()->where('id',$input['id'])->update($data);

        $this->changeRoleData($input);
    }


    public function store($input)
    {
        $user = auth()->guard('jwt')->user();
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
        $data = DatabaseLogic::filterTableData(UserModel::FIELDS,$input);
        unset($data['id']);
        UserModel::query()->insert($data);

        $this->changeRoleData($input);
    }


    private function changeRoleData($input)
    {
        // 改角色
        $roleIds = $input['roleIds'] ?? '';
        if(!empty($roleIds)){
            Role2UserModel::query()->where('user_id',$input['id'])->delete();
            $insertDatas = (function() use($roleIds,$input){
                $res = [];
                foreach ($roleIds as $k => $v){
                    $res[] = [
                        'user_id' => $input['id'],
                        'role_id' => $v
                    ];
                }
                return $res;
            })();
            Role2UserModel::query()->insert($insertDatas);
        }
    }

    public function getOne($input)
    {
        $row = UserModel::query()->where('id',$input['id'])->first();
        if(!is_null($row)){
            unset($row->salt);
            unset($row->password);
            return [
                'data' => $row,
                'roles' => array_column($row->its_roles->toArray(),'name'),
                'roleIds' => array_column($row->its_roles->toArray(),'id'),
                'auth_api' => UserModel::getOperatableUrlsByUser($row->id),
            ];
        }else{
            return [
                'data' => [],
                'roles' => [],
                'roleIds' => [],
                'auth_api' => [],
            ];
        }
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
        Db::beginTransaction();
        try{
            UserModel::query()->where('id',$input['id'])->delete();

            Role2UserModel::query()->where('user_id',$input['id'])->delete();

            Db::commit();
        }catch (\Exception $e){
            Db::rollBack();
            throw new AdminResponseException(ErrorCode::SYSTEM_INNER_ERROR,$e->getMessage());
        }
    }


    /**
     * 获取当前用心信息
     */
    public function userInfo(){
        $user = auth()->guard('jwt')->user();
        return [
            'data' => $user,
            'roles' => array_column($user->its_roles->toArray(),'name'),
            'auth_api' => UserModel::getOperatableUrlsByUser($user->id),
        ];
    }

}