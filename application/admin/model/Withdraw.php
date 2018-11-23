<?php

namespace app\admin\model;

use think\Model;

class Withdraw extends Model
{
    // 表名
    protected $name = 'withdraw';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'withdrawtime_text',
        'paytime_text',
        'status_text'
    ];
    

    
    public function getStatusList()
    {
        return ['0' => __('Status 0'),'1' => __('Status 1'),'2' => __('Status 2'),'-1' => __('Status -1')];
    }     


    public function getWithdrawtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['withdrawtime']) ? $data['withdrawtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getPaytimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['paytime']) ? $data['paytime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setWithdrawtimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setPaytimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


}
