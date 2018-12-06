<?php

namespace app\api\model;

use think\Model;
use traits\model\SoftDelete;

class Schoolcat extends Model
{
    // 表名
    protected $name = 'school_cat';

    use SoftDelete;
    protected $deleteTime = 'delete_time';
}
