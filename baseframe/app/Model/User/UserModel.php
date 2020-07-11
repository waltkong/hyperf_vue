<?php
declare(strict_types=1);

namespace App\Model\User;

use App\Model\Model;
use Hyperf\Database\Model\SoftDeletes;
use Qbhy\HyperfAuth\AuthAbility;
use Qbhy\HyperfAuth\Authenticatable;

class UserModel extends Model  implements Authenticatable{

    use SoftDeletes;

    use AuthAbility; //使用主键来做授权

    protected $connection = 'default';

    protected $table = 'user_user';

    protected $primaryKey = 'id';

    public $timestamps = true;

    // 用户和角色多对多
    public function its_roles()
    {
        return $this->belongsToMany(RoleModel::class, 'user_role_user', 'user_id', 'role_id');
    }

    // 一个用户所属一个公司
    public function its_company()
    {
        return $this->belongsTo(CompanyModel::class, 'user_id', 'id');
    }

    const ADMIN_STATUS = [
        'SUPER' => 1,
        'NORMAL' => 2,
    ];

    const FIELDS = [
        'id',
        'nickname',
        'username',
        'mobile',
        'password',
        'salt',
        'avatar',
        'company_id',
        'login_status',
        'admin_status',
    ];



}

