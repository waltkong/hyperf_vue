<?php
declare(strict_types=1);

namespace App\Model\System;

use App\Model\Model;
use App\Model\User\RoleModel;
use Hyperf\Database\Model\SoftDeletes;

class MenuModel extends Model {

    use SoftDeletes;

    protected $connection = 'default';

    protected $table = 'system_menu';

    protected $primaryKey = 'id';

    public $timestamps = true;

    // 角色和菜单多对多
    public function its_roles()
    {
        return $this->belongsToMany(RoleModel::class, 'user_role_menu', 'menu_id', 'role_id');
    }

    const NEED_AUTH = [
        'YES' => 1,
        'NO' => 0,
    ];

    const IS_MENU = [
        'YES' => 1,
        'NO' => 0,
    ];

    const IS_ONLY_SUPER_ADMIN = [
        'YES' => 1,
        'NO' => 0,
    ];

    const FIELDS = [
        'id',
        'company_id',
        'parent_id',
        'level',
        'name',
        'url',
        'need_auth',
        'is_menu',
        'is_only_super_admin',
    ];

}

