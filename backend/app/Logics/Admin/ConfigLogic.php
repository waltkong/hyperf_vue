<?php
namespace App\Logics\Admin;

use App\Constants\ErrorCode;
use App\Exception\AdminResponseException;
use App\Logics\Common\DatabaseLogic;
use App\Logics\Common\PageLogic;
use App\Model\System\ConfigModel;
use App\Model\User\CompanyModel;

class ConfigLogic{


    public function __construct()
    {

    }

    public function dataList($input)
    {
        $qsFunc = function () use($input){
            $obj = ConfigModel::query();
            if(isset($input['group_name']) && strlen($input['group_name']) > 0){
                $obj = $obj->where('group_name','like',"%{$input['group_name']}%");
            }
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
        try{
            $user = auth()->guard('jwt')->user();
            $company = $user->its_company;
            if($company->admin_status != CompanyModel::ADMIN_STATUS['SUPER']){
                //不为超管公司 is_global 参数无效 复写
                $input['is_global'] = ConfigModel::IS_GLOBAL['NO'];
            }
            if(empty($id)){
                $this->store($input);
            }else{
                $this->update($input);
            }
        }catch (\Exception $e){
            throw new AdminResponseException(ErrorCode::SYSTEM_INNER_ERROR,$e->getMessage());
        }

    }

    public function update($input)
    {
        $user = auth()->guard('jwt')->user();
        $findcheck = ConfigModel::query()
            ->where('group_name',$input['group_name'])
            ->where('name',$input['name'])
            ->where('id','<>',$input['id'])
            ->where('company_id',$user->company_id)
            ->exists();
        if($findcheck){
            throw new AdminResponseException(ErrorCode::ERROR,"该组名该配置名称已存在");
        }
        $data = DatabaseLogic::filterTableData(ConfigModel::FIELDS,$input);
        ConfigModel::query()->where('id',$input['id'])->update($data);
    }


    public function store($input)
    {
        $user = auth()->guard('jwt')->user();
        $findcheck = ConfigModel::query()
            ->where('group_name',$input['group_name'])
            ->where('name',$input['name'])
            ->where('company_id',$user->company_id)
            ->exists();
        if($findcheck){
            throw new AdminResponseException(ErrorCode::ERROR,"该组名该配置名称已存在");
        }
        $data = DatabaseLogic::filterTableData(ConfigModel::FIELDS,$input);
        unset($data['id']);
        ConfigModel::query()->insert($data);
    }

    public function getOne($input)
    {
        $row = ConfigModel::query()->where('id',$input['id'])->first();
        DatabaseLogic::commonCheckThisCompany($row);
        return [
            'data' => $row,
        ];
    }

    public function deleteOne($input)
    {
        try{
            DatabaseLogic::commonCheckThisCompany(ConfigModel::query()->where('id',$input['id'])->first());

            ConfigModel::query()->where('id',$input['id'])->delete();
        }catch (\Exception $e){
            throw new AdminResponseException(ErrorCode::SYSTEM_INNER_ERROR,$e->getMessage());
        }
    }

}