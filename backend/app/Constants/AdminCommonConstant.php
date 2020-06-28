<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class AdminCommonConstant extends AbstractConstants
{

    const EACH_PAGE = 20 ;// 每页默认显示数量

    ## 默认id倒序
    const ORDER_BY = "id";
    const ORDER_WAY = "desc";

    ## 表单请求中，通用的一些字段

    ## 列表请求时，经常会携带查询开始时间 结束时间，这里规定接口中使用的字段
    const FORM_START_AT = "start_at";
    const FORM_END_AT = "end_at";
    ##  开始时间和结束时间的数据类型 datetime or timestamp
    const FORM_START_END_TIME_TYPE = 'datetime';

    ## 分页排序字段
    const FORM_PAGE_INDEX = 'page_index';
    const FORM_EACH_PAGE = 'each_page';
    const FORM_ORDER_BY = 'order_by';
    const FORM_ORDER_WAY = 'order_way';



}
