<?php
namespace App\Logics\Admin;

use App\Constants\ErrorCode;
use App\Exception\AdminResponseException;
use App\Model\System\LogModel;
use App\Logics\Common\DatabaseLogic;
use App\Logics\Common\PageLogic;

class LogLogic{

    public function __construct()
    {
    }

    public function dataList($input)
    {

        $qsFunc = function () use($input){
            $obj = LogModel::query();
            if(isset($input['user_id']) && strlen($input['user_id']) > 0){
                $obj = $obj->where('user_id','=',"{$input['user_id']}");
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

}