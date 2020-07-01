<?php
declare(strict_types=1);

namespace App\Model\User;

use App\Model\Model;
use Hyperf\Database\Model\SoftDeletes;

class CompanyModel extends Model {

    use SoftDeletes;

    protected $connection = 'default';

    protected $table = 'user_company';

    protected $primaryKey = 'id';

    public $timestamps = true;

    //一个公司多个users
    public function its_users()
    {
        return $this->hasMany(UserModel::class, 'company_id', 'id');
    }

    const ADMIN_STATUS = [
        'SUPER' => 1,
        'NORMAL' => 2,
    ];


    const FIELDS = [
        'id',
        'name',
        'remark',
        'phone',
        'contact_user',
        'admin_status',
        'created_at',
        'deleted_at',
        'updated_at',
    ];


}