<?php
namespace App\Logics\Common;

use App\Constants\AdminCommonConstant;
use App\Model\User\CompanyModel;
use PDepend\Util\Log;

class PageLogic{


    /**
     *  根据 用户表单参数，获取分页和排序参数
     * @param array $input
     * @return array
     */
    public static function getPageParams(array $input){

        $page_index = $input[AdminCommonConstant::FORM_PAGE_INDEX] ?? 1;
        $each_page = $input[AdminCommonConstant::FORM_EACH_PAGE] ?? AdminCommonConstant::EACH_PAGE;
        $page_offset = ((int)$each_page) * ((int)$page_index - 1);
        $order_by = $input[AdminCommonConstant::FORM_ORDER_BY] ?? AdminCommonConstant::ORDER_BY;
        $order_way = $input[AdminCommonConstant::FORM_ORDER_WAY] ?? AdminCommonConstant::ORDER_WAY;

        return [
            'page_index' => $page_index,
            'each_page' => $each_page,
            'page_offset' => $page_offset,
            'order_by' => $order_by,
            'order_way' => $order_way
        ];
    }


    /**
     *  查询构造器 拼接 分页和排序 ，返回查询构造器
     * @param $querySet
     * @param array $input
     * @return mixed
     */
    public static function getPaginateListQuerySet($querySet, array $input){

        $pageParams = self::getPageParams($input);

        return  $querySet->orderBy($pageParams['order_by'], $pageParams['order_way'])
            ->offset($pageParams['page_offset'])->limit($pageParams['each_page']);

    }


    /**
     *  通用的列表数据返回
     * @param array $data
     * @return array
     */
    public static function commonListDataReturn($data = []){
        $default = [
            'data' => [],
            'total' => 0,
            'count' => 0,
        ];
        foreach ($data as $k => $v){
            $default[$k] = $v;
        }
        return $default;
    }


    /**
     *  对于普通公司，查询时一定要拼接上自己的公司id，谨防查询到别的公司
     * @param $qs
     * @param $data
     * @return mixed
     */
    public static function attachCompanyQuerySetFilter($qs, $data){
        $user = auth()->guard('jwt')->user();
        $company = $user->its_company;

        Log::debug(\GuzzleHttp\json_encode($company));

        if(!isset($data['company_id']) ||  empty($data['company_id'])){
            $qs = $qs->where('company_id',$user->company_id);
            return $qs;
        }
        return $qs;
    }


    /**
     * 如果携带开始时间和结束时间 进行查询构造器的过滤
     * @param $qs
     * @param $data
     * @param string $field 以哪个字段来做时间的过滤
     * @param string $field_type  'datetime'表示表中是 2020-01-01 01:01:01形式  'timestamp' 表示表中时间是 1472345267 形式
     */
    public static function startAndEndTimeQuerySetFilter($qs, $data, $field='created_at', $field_type='datetime'){
        $t_s = AdminCommonConstant::FORM_START_AT;
        $t_e = AdminCommonConstant::FORM_END_AT;
        $typ = AdminCommonConstant::FORM_START_END_TIME_TYPE;

        if(isset($data[$t_s]) && strlen($data[$t_s])>0){
            if($typ == 'datetime'){
                if($field_type == 'datetime'){
                    $qs = $qs->where($field, '>=', $data[$t_s]);
                }else{
                    $qs = $qs->where($field, '>=', strtotime($data[$t_s]));
                }
            }else{
                if($field_type == 'datetime'){
                    $qs = $qs->where($field, '>=', date('Y-m-d H:i:s',$data[$t_s]) );
                }else{
                    $qs = $qs->where($field, '>=', $data[$t_s]);
                }
            }
        }

        if(isset($data[$t_e]) && strlen($data[$t_e])>0){
            if($typ == 'datetime'){
                if($field_type == 'datetime'){
                    $qs = $qs->where($field, '<=', $data[$t_e]);
                }else{
                    $qs = $qs->where($field, '<=', strtotime($data[$t_e]));
                }
            }else{
                if($field_type == 'datetime'){
                    $qs = $qs->where($field, '<=', date('Y-m-d H:i:s',$data[$t_e]));
                }else{
                    $qs = $qs->where($field, '<=', $data[$t_e]);
                }
            }
        }

        return $qs;

    }


}