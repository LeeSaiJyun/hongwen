<?php

namespace app\admin\model;

use think\Model;

class Reservation extends Model
{
    // 表名
    protected $name = 'reservation';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'status_text',
        'appointedtime_text'
    ];
    

    
    public function getStatusList()
    {
        return ['-1' => __('Status -1'),'0' => __('Status 0'),'1' => __('Status 1')];
    }     


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getAppointedtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['appointedtime']) ? $data['appointedtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setAppointedtimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


}
