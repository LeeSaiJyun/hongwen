<?php

namespace app\api\model;

use think\Model;
use traits\model\SoftDelete;

class Schoolmajor extends Model
{
    // 表名
    protected $name = 'school_major';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;


    use SoftDelete;
    protected $deleteTime = 'delete_time';




}
