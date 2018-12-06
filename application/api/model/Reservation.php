<?php

namespace app\api\model;

use think\Model;

class Reservation extends Model
{
    // 表名
    protected $name = 'reservation';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    protected function setAppointedtimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

}
