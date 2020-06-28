<?php
namespace App\Logics\Common;

use App\Constants\ErrorCode;
use App\Exception\AdminResponseException;

class ValidateLogic{


    public function __construct()
    {
    }

    protected $factory;

    public function setValidateFactory($factory){
        $this->factory = $factory;
        return $this;
    }


    /**
     *  admin模块通用的验证器
     * @param array $input
     * @param array $rule
     * @param array $rule_msg
     */
    public function commonAdminValidate($input=[], $rule=[], $rule_msg=[]){
        $validator = $this->factory->make($input,
            $rule,
            $rule_msg
        );
        if ($validator->fails()){
            $errorMessage = $validator->errors()->first();
            throw new AdminResponseException(ErrorCode::ERROR, $errorMessage);
        }
    }


}