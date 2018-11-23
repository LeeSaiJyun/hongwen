<?php

namespace app\admin\model;

use think\Model;

class Bank extends Model
{
    // 表名
    protected $name = 'user_bank';
    
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


    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
