<?php
namespace App\Logics\Common;

use App\Constants\ErrorCode;

class ResponseLogic{

    public static function successData(array $data = []){
        return [
            'code' => ErrorCode::SUCCESS,
            'msg' => 'OK',
            'data' => $data
        ];
    }


    public static function errorData($code,$msg='',$data=[]){
        return [
            'code' => $code,
            'msg' => empty($msg)? ErrorCode::getMessage($code) : $msg,
            'data' => $data
        ];
    }


}
