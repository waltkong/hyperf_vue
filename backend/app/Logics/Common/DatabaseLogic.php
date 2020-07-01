<?php
namespace App\Logics\Common;

use App\Constants\ErrorCode;
use App\Exception\AdminResponseException;

class DatabaseLogic{

    /**
     *  过滤掉数据表中不存在的数据
     * @param $modelClass
     * @param array $data
     * @return array
     */
    public static function filterTableData(array $tableFields , array $data){

        $fields = $tableFields;

        $result = [];

        foreach ($data as $k => $v){
            if(in_array($k,$fields)){
                $result[$k] = $v;
            }
        }

        return $result;
    }


    /**
     * 通用的插入
     * @param $modelClass
     * @param array $data
     * @return mixed
     */
    public static function commonInsertData($modelClass, array $data){

        $fields = $modelClass::FIELDS;

        $data = self::filterTableData($fields, $data);

        //只有不存在company_id的时候才会覆盖
        if(!isset($data['company_id']) && empty($data['company_id'])){
            if(auth()->guard('jwt')->check()){

                $user = auth()->guard('jwt')->user();

                //多拼接一个所属公司id
                if(in_array('company_id',$fields) && !isset($data['company_id'])){
                    $data['company_id'] = $user->company_id  ;
                }
            }
        }

        return $modelClass::query()->insertGetId($data);

    }


    /**
     *  通用的编辑
     * @param $modelClass
     * @param $qs
     * @param array $data
     * @return mixed
     */
    public static function commonUpdateData($modelClass, $qs, array $data){

        $fields = $modelClass::FIELDS;

        $data = self::filterTableData($fields, $data);

        if(!isset($data['company_id']) && empty($data['company_id'])){
            if(auth()->guard('jwt')->check()){

                $user = auth()->guard('jwt')->user();

                //多拼接一个所属公司id
                if(in_array('company_id',$fields) && !isset($data['company_id'])){
                    $data['company_id'] = $user->company_id  ;
                }
            }
        }

        return $qs->update($data);

    }


    public static function commonCheckThisCompany($row,$field='company_id'){
        $user = auth()->guard('jwt')->user();

        if(!is_null($row)){
            if($row->$field != $user->company_id){
                throw new AdminResponseException(ErrorCode::ERROR, "非法操作");
            }
        }

    }

}