<?php

namespace app\admin\model;

use think\Model;

class Schoolmajor extends Model
{
    // 表名
    protected $name = 'school_major';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'is_del_text'
    ];
    

    
    public function getIsDelList()
    {
        return ['0' => __('Is_del 0'),'1' => __('Is_del 1')];
    }     


    public function getIsDelTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['is_del']) ? $data['is_del'] : '');
        $list = $this->getIsDelList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function major()
    {
        return $this->belongsTo('Major', 'major_ids', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function school()
    {
        return $this->belongsTo('School', 'school_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
