<?php
namespace App\Logics\Admin;

use App\Constants\ErrorCode;
use App\Exception\AdminResponseException;
use App\Model\User\RoleModel;
use App\Logics\Common\DatabaseLogic;
use App\Logics\Common\PageLogic;

class RoleLogic{

    public function __construct()
    {
    }

    public function dataList($input)
    {
        $qsFunc = function () use($input){
            $obj = RoleModel::query();
            if(isset($input['name']) && strlen($input['name']) > 0){
                $obj = $obj->where('name','like',"%{$input['name']}%");
            }
            $obj = PageLogic::startAndEndTimeQuerySetFilter($obj,$input);
            $obj = PageLogic::superCompanyQuerySetFilter($obj,$input);
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

            if(empty($id)){
                $findcheck = RoleModel::query()
                    ->where('name',$input['name'])
                    ->where('company_id',$user->company_id)
                    ->exists();
                if($findcheck){
                    throw new AdminResponseException(ErrorCode::ERROR,"该名称已存在");
                }

                DatabaseLogic::commonInsertData(RoleModel::class,$input);

            }else{
                $findcheck = RoleModel::query()
                    ->where('name',$input['name'])
                    ->where('id','<>',$id)
                    ->where('company_id',$user->company_id)
                    ->exists();
                if($findcheck){
                    throw new AdminResponseException(ErrorCode::ERROR,"该名称已存在");
                }
                $qs = RoleModel::query()->where('id',$id);

                DatabaseLogic::commonUpdateData(RoleModel::class,$qs,$input);

            }
        }catch (\Exception $e){
            throw new AdminResponseException(ErrorCode::SYSTEM_INNER_ERROR,$e->getMessage());
        }
    }

    public function getOne($input)
    {
        $row = RoleModel::query()->where('id',$input['id'])->first();
        DatabaseLogic::commonCheckThisCompany($row);
        return [
            'data' => $row,
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

}