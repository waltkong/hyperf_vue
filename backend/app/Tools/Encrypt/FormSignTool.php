<?php
namespace App\Tools\Encrypt;

class FormSignTool{

    /**
    *  进行加密
    * @param $api_key string 秘钥
    * @param array $verifyData
    * @return string
    */
    public static function setSign($api_key, $verifyData=[]){
        $str = '';
        if(!empty($verifyData)){
            ksort($verifyData);
            $strarr = [];
            foreach ($verifyData as $k => $item){
                $strarr[] = "{$k}={$item}";
            }
            $str .= implode('&',$strarr);
        }
        //拼接key
        $str .= $api_key;
        //加密
        $ret =  strtolower(md5($str));
        $ret = strtolower(sha1($ret));
        return $ret;
    }

    /**
     * 校验签名
     * @param $api_key
     * @param array $verifyData
     * @param $sign
     * @return boolean
     */
    public static function checkSign($api_key, $verifyData=[] , $sign){
        if($sign != self::setSign($api_key, $verifyData)){
            return false;
        }
        return true;
    }

}