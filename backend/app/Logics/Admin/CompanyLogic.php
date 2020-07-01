<?php
namespace App\Logics\Admin;

use App\Constants\ErrorCode;
use App\Exception\AdminResponseException;
use App\Logics\Common\DatabaseLogic;
use App\Logics\Common\PageLogic;
use App\Model\User\CompanyModel;
use PDepend\Util\Log;

class CompanyLogic{

    public function __construct()
    {
    }

    public function dataList($input)
    {
        $qsFunc = function () use($input){
            $obj = CompanyModel::query();
            if(isset($input['name']) && strlen($input['name']) > 0){
                $obj = $obj->where('name','like',"%{$input['name']}%");
            }
            if(isset($input['admin_status']) && strlen($input['admin_status']) > 0){
                $obj = $obj->where('admin_status','=',$input['admin_status']);
            }
            $obj = PageLogic::startAndEndTimeQuerySetFilter($obj,$input);
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
        $findcheck = CompanyModel::query()->where('name',$input['name'])->where('id','<>',$input['id'])->exists();
        if($findcheck){
            throw new AdminResponseException(ErrorCode::ERROR,"公司名已存在");
        }
        $data = DatabaseLogic::filterTableData(CompanyModel::FIELDS,$input);
        CompanyModel::query()->where('id',$input['id'])->update($data);
    }


    public function store($input)
    {
        $user = auth()->guard('jwt')->user();
        $findcheck = CompanyModel::query()->where('name',$input['name'])->exists();
        if($findcheck){
            throw new AdminResponseException(ErrorCode::ERROR,"公司名已存在");
        }
        $data = DatabaseLogic::filterTableData(CompanyModel::FIELDS,$input);
        unset($data['id']);
        CompanyModel::query()->insert($data);
    }

    public function getOne($input)
    {
        $row = CompanyModel::query()->where('id',$input['id'])->first();

        if(!is_null($row)){
            DatabaseLogic::commonCheckThisCompany($row,'id');
        }
        return [
            'data' => $row,
        ];
    }

    public function deleteOne($input)
    {

        try{
            DatabaseLogic::commonCheckThisCompany(CompanyModel::query()->where('id',$input['id'])->first(),'id');

            CompanyModel::query()->where('id',$input['id'])->delete();
        }catch (\Exception $e){
            throw new AdminResponseException(ErrorCode::SYSTEM_INNER_ERROR,$e->getMessage());
        }

    }


    public function checkCompanyIsSuper($companyId)
    {
       $row =  CompanyModel::query()->where('id',$companyId)->first();
       if(!is_null($row) && $row->admin_status == CompanyModel::ADMIN_STATUS['SUPER']){
           return true;
       }
       return false;
    }

}