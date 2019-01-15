<?php

namespace app\admin\model;

use think\Model;

class Order extends Model
{
    // 表名
    protected $name = 'order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'paymentdata_text',
        'paytime_text'
    ];
    

    
    public function getPaymentdataList()
    {
        return ['normal' => __('Normal'),'hidden' => __('Hidden')];
    }     


    public function getPaymentdataTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['paymentdata']) ? $data['paymentdata'] : '');
        $list = $this->getPaymentdataList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getPaytimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['paytime']) ? $data['paytime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setPaytimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    public function user()
    {
        return $this->hasOne('User', 'id', 'user_id', [], 'LEFT')->field('id,realname');
    }

}
