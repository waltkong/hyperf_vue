<?php
declare(strict_types=1);

namespace App\Model\User;

use App\Model\Model;


/**
 *  角色-菜单 对应表
 * Class Role2UserModel
 * @package App\Model\User
 */
class Role2MenuModel extends Model {

    protected $connection = 'default';

    protected $table = 'user_role_menu';

    protected $primaryKey = 'id';

    public $timestamps = true;

    const FIELDS = [
        'id',
        'role_id',
        'menu_id',
    ];


}

