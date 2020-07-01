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
     *  是否操作的数据是该用户所在的公司
     * @param $row
     * @param string $field
     */
    public static function commonCheckThisCompany($row, $field='company_id'){
        $user = auth()->guard('jwt')->user();

        if(!is_null($row)){
            if($row->$field != $user->company_id){
                throw new AdminResponseException(ErrorCode::ERROR, "非法操作");
            }
        }

    }

}