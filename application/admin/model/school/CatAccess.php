<?php

namespace app\admin\model\school;

use think\Model;
use think\model\Pivot;

class CatAccess extends Pivot
{
    // 表名
    protected $name = 'school_cat_access';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [

    ];
    
    public function school()
    {
        return $this->belongsTo('School', 'school_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function cat()
    {
        return $this->belongsTo('Cat', 'school_cat_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
