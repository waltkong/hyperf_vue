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

use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');


//后台需要token访问的
Router::addGroup('/admin',function (){

    ## 用户模块
    Router::post('/user/dataList', 'App\Controller\Admin\UserController@dataList');
    Router::post('/user/storeOrUpdate', 'App\Controller\Admin\UserController@storeOrUpdate');
    Router::post('/user/changePassword', 'App\Controller\Admin\UserController@changePassword');
    Router::post('/user/getOne', 'App\Controller\Admin\UserController@getOne');
    Router::post('/user/deleteOne', 'App\Controller\Admin\UserController@deleteOne');
    Router::post('/user/userInfo', 'App\Controller\Admin\UserController@userInfo');


    Router::post('/role/dataList', 'App\Controller\Admin\RoleController@dataList');
    Router::post('/role/storeOrUpdate', 'App\Controller\Admin\RoleController@storeOrUpdate');
    Router::post('/role/getOne', 'App\Controller\Admin\RoleController@getOne');
    Router::post('/role/deleteOne', 'App\Controller\Admin\RoleController@deleteOne');
    Router::post('/role/getAllMenus', 'App\Controller\Admin\RoleController@getAllMenus');
    Router::post('/role/getThisRoleMenus', 'App\Controller\Admin\RoleController@getThisRoleMenus');
    Router::post('/role/thisCompanyRoleOptions', 'App\Controller\Admin\RoleController@thisCompanyRoleOptions');


    Router::post('/company/dataList', 'App\Controller\Admin\CompanyController@dataList');
    Router::post('/company/storeOrUpdate', 'App\Controller\Admin\CompanyController@storeOrUpdate');
    Router::post('/company/getOne', 'App\Controller\Admin\CompanyController@getOne3');
    Router::post('/company/deleteOne', 'App\Controller\Admin\CompanyController@deleteOne');
    Router::post('/company/companyOptions', 'App\Controller\Admin\CompanyController@companyOptions');


    ## 系统模块
    Router::post('/menu/dataList', 'App\Controller\Admin\MenuController@dataList');
    Router::post('/menu/storeOrUpdate', 'App\Controller\Admin\MenuController@storeOrUpdate');
    Router::post('/menu/getOne', 'App\Controller\Admin\MenuController@getOne');
    Router::post('/menu/deleteOne', 'App\Controller\Admin\MenuController@deleteOne');
    Router::post('/menu/menuParentOptions', 'App\Controller\Admin\MenuController@menuParentOptions');


    Router::post('/config/dataList', 'App\Controller\Admin\ConfigController@dataList');
    Router::post('/config/storeOrUpdate', 'App\Controller\Admin\ConfigController@storeOrUpdate');
    Router::post('/config/getOne', 'App\Controller\Admin\ConfigController@getOne');
    Router::post('/config/deleteOne', 'App\Controller\Admin\ConfigController@deleteOne');


    Router::post('/log/dataList', 'App\Controller\Admin\LogController@dataList');



},[
    'middleware' => [
        App\Middleware\AdminTokenVerifyMiddleware::class, //用户身份信息认证中间件
        App\Middleware\NotFoundMiddleware::class, // 找不到路由中间件
        App\Middleware\AdminOperateAuthMiddleware::class, // 操作权限中间件
    ]
]);






