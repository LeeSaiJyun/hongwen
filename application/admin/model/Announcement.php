<?php

namespace app\admin\model;

use think\Model;

class Announcement extends Model
{
    // 表名
    protected $name = 'announcement';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'status_text',
        'is_del_text'
    ];
    

    
    public function getStatusList()
    {
        return ['0' => __('Status 0'),'1' => __('Status 1')];
    }     

    public function getIsDelList()
    {
        return ['0' => __('Is_del 0'),'1' => __('Is_del 1')];
    }     


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsDelTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['is_del']) ? $data['is_del'] : '');
        $list = $this->getIsDelList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
