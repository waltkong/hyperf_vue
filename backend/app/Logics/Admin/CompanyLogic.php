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
                $findcheck = CompanyModel::query()->where('name',$input['name'])->exists();
                if($findcheck){
                    throw new AdminResponseException(ErrorCode::ERROR,"公司名已存在");
                }
                DatabaseLogic::commonInsertData(CompanyModel::class,$input);

            }else{
                $findcheck = CompanyModel::query()->where('name',$input['name'])->where('id','<>',$id)->exists();
                if($findcheck){
                    throw new AdminResponseException(ErrorCode::ERROR,"公司名已存在");
                }
                $qs = CompanyModel::query()->where('id',$id);

                DatabaseLogic::commonUpdateData(CompanyModel::class,$qs,$input);

            }
        }catch (\Exception $e){
            throw new AdminResponseException(ErrorCode::SYSTEM_INNER_ERROR,$e->getMessage());
        }

    }

    public function getOne($input)
    {
        $row = CompanyModel::query()->where('id',$input['id'])->first();

        if(!is_null($row)){
            DatabaseLogic::commonCheckThisCompany($row,'id');
        }

        Log::log(\GuzzleHttp\json_encode($input));

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

}