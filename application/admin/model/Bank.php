<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Bank extends Model
{
    // 表名
    protected $name = 'user_bank';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;

    use SoftDelete;
    protected $deleteTime = 'delete_time';



    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
