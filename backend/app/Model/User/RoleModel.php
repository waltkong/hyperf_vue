<?php
declare(strict_types=1);

namespace App\Model\User;

use App\Model\Model;
use App\Model\System\MenuModel;
use Hyperf\Database\Model\SoftDeletes;

class RoleModel extends Model {

    use SoftDeletes;

    protected $connection = 'default';

    protected $table = 'user_role';

    protected $primaryKey = 'id';

    public $timestamps = true;

    // 用户和角色多对多
    public function its_users()
    {
        return $this->belongsToMany(UserModel::class, 'user_role_user', 'role_id', 'user_id');
    }

    // 角色和菜单多对多
    public function its_menus()
    {
        return $this->belongsToMany(MenuModel::class, 'user_role_menu', 'role_id', 'menu_id');
    }

    const FIELDS = [
        'id',
        'name',
        'remark',
        'company_id',
    ];




}

