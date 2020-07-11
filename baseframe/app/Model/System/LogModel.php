<?php
declare(strict_types=1);

namespace App\Model\System;

use App\Model\Model;
use Hyperf\Database\Model\SoftDeletes;

class LogModel extends Model {

    use SoftDeletes;

    protected $connection = 'default';

    protected $table = 'system_log';

    protected $primaryKey = 'id';

    public $timestamps = true;

    const FIELDS = [
        'id',
        'url',
        'param',
        'user_id',
        'company_id',
        'ip',
    ];


}