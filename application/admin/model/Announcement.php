<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Announcement extends Model
{
    // 表名
    protected $name = 'announcement';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;

    use SoftDelete;
    protected $deleteTime = 'delete_time';


}
