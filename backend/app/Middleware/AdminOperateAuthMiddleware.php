<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\ErrorCode;
use App\Model\System\MenuModel;
use App\Model\User\CompanyModel;
use App\Model\User\Role2UserModel;
use App\Model\User\RoleModel;
use App\Model\User\UserModel;
use function FastRoute\TestFixtures\empty_options_cached;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * admin用户操作权限
 * Class AdminOperateAuthMiddleware
 * @package App\Middleware
 */
class AdminOperateAuthMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var HttpResponse
     */
    protected $response;

    private $debug = [];

    public function __construct(ContainerInterface $container, HttpResponse $response, RequestInterface $request)
    {
        $this->container = $container;
        $this->response = $response;
        $this->request = $request;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try{
            $userRow = auth()->guard('jwt')->user();

            $url = $this->request->getRequestUri();

            $checkres = $this->checkOperateAuth($userRow,$url);

            if(!$checkres){
                throw new \Exception("没有操作权限");
            }

        }catch (\Exception $e){
            return $this->response->json(
                [
                    'code' => ErrorCode::PERMISSION_NO_ACCESS,
                    'msg' => $e->getMessage(),
                    'data' => [],
                    'debug' => $this->debug
                ]
            );
        }

        return $handler->handle($request);
    }


    protected function checkOperateAuth($userRow,$url){

        $menuRow = MenuModel::query()
            ->where('url',$url)
            ->first();

        if(is_null($menuRow)){
            return true;
        }

        if($menuRow->need_auth != MenuModel::NEED_AUTH['YES']){
            return true;
        }

        $its_company = CompanyModel::query()->where('id',$userRow->company_id)->first();
        if(is_null($its_company)){
            $this->debug = [
                'msg' => 'no company belong',
            ];
            return false;
        }

        if($menuRow->is_only_super_company == MenuModel::IS_ONLY_SUPER_COMPANY['YES'] && $its_company->admin_status != CompanyModel::ADMIN_STATUS['SUPER']){
            $this->debug = 'only super company has permission';
            return false;
        }

        if($menuRow->is_only_super_company == MenuModel::IS_ONLY_SUPER_COMPANY['NO'] && $userRow->admin_status == UserModel::ADMIN_STATUS['SUPER']){
            return true;
        }

        //必须为超管，但是用户不是超管，则没有权限。
        if($menuRow->is_only_super_admin==MenuModel::IS_ONLY_SUPER_ADMIN['YES'] &&  $userRow->admin_status != UserModel::ADMIN_STATUS['SUPER']){
            $this->debug = 'only super admin has permission';
            return false;
        }


        $roleRowsByMenu = $menuRow->its_roles;

        $roleIds = Role2UserModel::query()->where('user_id',$userRow->id)->pluck('role_id');
        $roleRowsByUser = RoleModel::query()->whereIn('id',$roleIds)->get();

        foreach ($roleRowsByMenu as $k1 => $v1){
            foreach ($roleRowsByUser as $k2 => $v2){
                if($v1->id == $v2->id){
                    return true;
                }
            }
        }

        return false;
    }
}