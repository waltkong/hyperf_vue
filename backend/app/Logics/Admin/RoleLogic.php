<?php
namespace App\Logics\Admin;

use App\Constants\ErrorCode;
use App\Exception\AdminResponseException;
use App\Model\System\MenuModel;
use App\Model\User\Role2MenuModel;
use App\Model\User\RoleModel;
use App\Logics\Common\DatabaseLogic;
use App\Logics\Common\PageLogic;
use App\Model\User\UserModel;
use App\Tools\DataConvert\TreeTool;
use Hyperf\DbConnection\Db;

class RoleLogic{

    protected $companyLogic;
    public function __construct(CompanyLogic $companyLogic)
    {
        $this->companyLogic = $companyLogic;
    }

    public function dataList($input)
    {
        $qsFunc = function () use($input){
            $obj = RoleModel::query();
            if(isset($input['name']) && strlen($input['name']) > 0){
                $obj = $obj->where('name','like',"%{$input['name']}%");
            }
            $obj = PageLogic::startAndEndTimeQuerySetFilter($obj,$input);
            $obj = PageLogic::attachCompanyQuerySetFilter($obj,$input);
            return $obj;
        };

        $list = PageLogic::getPaginateListQuerySet($qsFunc(),$input)->get()->toArray();

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
        $findcheck = RoleModel::query()
            ->where('name',$input['name'])
            ->where('id','<>',$input['id'])
            ->where('company_id',$user->company_id)
            ->exists();
        if($findcheck){
            throw new AdminResponseException(ErrorCode::ERROR,"该名称已存在");
        }
        $data = DatabaseLogic::filterTableData(RoleModel::FIELDS,$input);
        RoleModel::query()->where('id',$input['id'])->update($data);

        $this->saveRoleMenuData($input);
    }


    private function saveRoleMenuData($input)
    {
        $menus =  $input['menus'] ?? '';
        Role2MenuModel::query()->where('role_id',$input['id'])->delete();
        if(!empty($menus)){
            $menusIds = MenuModel::query()->whereIn('url',$menus)->pluck('id');
            $tmp = [];
            foreach ($menusIds as  $menusId){
                $tmp[] = [
                    'menu_id' => $menusId,
                    'role_id' => $input['id'],
                ];
            }
            Role2MenuModel::query()->insert($tmp);
        }
    }


    public function store($input)
    {
        $user = auth()->guard('jwt')->user();
        $findcheck = RoleModel::query()
            ->where('name',$input['name'])
            ->where('company_id',$user->company_id)
            ->exists();
        if($findcheck){
            throw new AdminResponseException(ErrorCode::ERROR,"该名称已存在");
        }
        $data = DatabaseLogic::filterTableData(RoleModel::FIELDS,$input);
        unset($data['id']);
        RoleModel::query()->insert($data);

        $this->saveRoleMenuData($input);
    }

    public function getOne($input)
    {
        $row = RoleModel::query()->where('id',$input['id'])->first();
        DatabaseLogic::commonCheckThisCompany($row);
        return [
            'data' => $row,
        ];
    }


    /**
     * 添加角色，授权的菜单
     * 如果非超管公司，则只把 is_only_super_company = 0 的菜单找出来
     */
    public function getAllMenus()
    {
        $user = auth()->guard('jwt')->user();
        $check = $this->companyLogic->checkCompanyIsSuper($user->company_id);
        if($check){
            $menus = MenuModel::query()->get()->toArray();
        }else{
            $menus = MenuModel::query()->where('is_only_super_company',MenuModel::IS_ONLY_SUPER_COMPANY['NO'])->get()->toArray();
        }
        $tree = (new TreeTool())->getTree($menus,0,'parent_id','id');

        return [
            'data' => $tree,
        ];
    }


    public function getThisRoleMenus($input)
    {
        $roleId = $input['id'];
        $menuIds = Role2MenuModel::query()->where('role_id',$roleId)->pluck('menu_id');
        $menus = MenuModel::query()
            ->whereIn('id',$menuIds)
            ->get()->toArray();
        $tree = (new TreeTool())->getTree($menus,0,'parent_id','id');
        return [
            'data' => $tree,
        ];
    }


    public function deleteOne($input)
    {
        try{
            DatabaseLogic::commonCheckThisCompany(RoleModel::query()->where('id',$input['id'])->first());

            RoleModel::query()->where('id',$input['id'])->delete();
        }catch (\Exception $e){
            throw new AdminResponseException(ErrorCode::SYSTEM_INNER_ERROR,$e->getMessage());
        }
    }

    public function thisCompanyRoleOptions()
    {
        $user = auth()->guard('jwt')->user();
        $roles = RoleModel::query()->where('company_id',$user->company_id)->get()->toArray();

        $resultFunc = function () use($roles){
            $ret = [];
            foreach ($roles as $k => $v){
                $ret[] = [
                    'key' => $v['id'],
                    'label' => $v['name'],
                ];
            }
            return $ret;
        };

        return [
            'data' => $resultFunc(),
        ];
    }

}