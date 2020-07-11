<?php
declare(strict_types=1);

namespace App\Model\System;

use App\Model\Model;
use Hyperf\Database\Model\SoftDeletes;

class ConfigModel extends Model {

    use SoftDeletes;

    protected $connection = 'default';

    protected $table = 'system_config';

    protected $primaryKey = 'id';

    public $timestamps = true;


    const FIELDS = [
        'id',
        'group_name',
        'name',
        'value',
        'is_global',
        'company_id',
    ];

    const IS_GLOBAL = [
        'YES' => 1,
        'NO' => 0,
    ];

    CONST TYPE = [
        'JSON' => 'json',
        'SEMICOMA' => 'semicoma',
        'STRING' => 'string',
        'STOP' => 'stop',
        'URL' => 'url',
    ];


}

