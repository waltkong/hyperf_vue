<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class ErrorCode extends AbstractConstants
{
    /**
     * @Message("Server Error！")
     */
    const SERVER_ERROR = 500;


    // 成功状态码
    const SUCCESS = 1;
    //未知异常状态码
    const ERROR = 0;

    // -------------------失败状态码----------------------
    // 参数错误
    const PARAMS_IS_NULL = 10001;// 参数为空
    const PARAMS_NOT_COMPLETE = 10002; // 参数不全
    const PARAMS_TYPE_ERROR = 10003; // 参数类型错误
    const PARAMS_IS_INVALID = 10004; // 参数无效

    // 用户错误
    const USER_NOT_EXIST = 20001; // 用户不存在
    const USER_NOT_LOGGED_IN = 20002; // 用户未登陆
    const USER_ACCOUNT_ERROR = 20003; // 用户名或密码错误
    const USER_ACCOUNT_FORBIDDEN = 20004; // 用户账户已被禁用
    const USER_HAS_EXIST = 20005;// 用户已存在
    const USER_TOKEN_ERROR = 20006;  //用户token异常

    // 业务错误
    const BUSINESS_ERROR = 30001;// 系统业务出现问题

    // 系统错误
    const SYSTEM_INNER_ERROR = 40001; // 系统内部错误

    // 数据错误
    const DATA_NOT_FOUND = 50001; // 数据未找到
    const DATA_IS_WRONG = 50002;// 数据有误
    const DATA_ALREADY_EXISTED = 50003;// 数据已存在

    // 接口错误
    const INTERFACE_INNER_INVOKE_ERROR = 60001; // 系统内部接口调用异常
    const INTERFACE_OUTER_INVOKE_ERROR = 60002;// 系统外部接口调用异常
    const INTERFACE_FORBIDDEN = 60003;// 接口禁止访问
    const INTERFACE_ADDRESS_INVALID = 60004;// 接口地址无效
    const INTERFACE_REQUEST_TIMEOUT = 60005;// 接口请求超时
    const INTERFACE_EXCEED_LOAD = 60006;// 接口负载过高

    // 权限错误
    const PERMISSION_NO_ACCESS = 70001;// 没有访问权限


    public static function getMessage($code){
        $map = [
            self::SUCCESS => 'ok',
            self::ERROR => 'error',
            self::SERVER_ERROR => 'server error',

            self::PARAMS_IS_NULL => '参数为空',
            self::PARAMS_NOT_COMPLETE => '参数不全',
            self::PARAMS_TYPE_ERROR => '参数类型错误',
            self::PARAMS_IS_INVALID => '参数无效',

            self::USER_NOT_EXIST => '用户不存在',
            self::USER_NOT_LOGGED_IN => '用户未登陆',
            self::USER_ACCOUNT_ERROR => '用户名或密码错误',
            self::USER_ACCOUNT_FORBIDDEN => '用户账户已被禁用',
            self::USER_HAS_EXIST => '用户已存在',
            self::USER_TOKEN_ERROR => '用户token异常',

            self::BUSINESS_ERROR => '系统业务出现问题',

            self::SYSTEM_INNER_ERROR => '系统内部错误',

            self::DATA_NOT_FOUND => '数据未找到',
            self::DATA_IS_WRONG => '数据有误',
            self::DATA_ALREADY_EXISTED => '数据已存在',

            self::INTERFACE_INNER_INVOKE_ERROR => '系统内部接口调用异常',
            self::INTERFACE_OUTER_INVOKE_ERROR => '系统外部接口调用异常',
            self::INTERFACE_FORBIDDEN => '接口禁止访问',
            self::INTERFACE_ADDRESS_INVALID => '接口地址无效',
            self::INTERFACE_REQUEST_TIMEOUT => '接口请求超时',
            self::INTERFACE_EXCEED_LOAD => '接口负载过高',

            self::PERMISSION_NO_ACCESS => '没有访问权限',

        ];

        return $map[$code] ?? 'undefined error';
    }


}
