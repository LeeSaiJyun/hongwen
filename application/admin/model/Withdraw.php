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
        'status_text'
    ];

    public function getStatusList()
    {
        return ['0' => __('Status 0'),'1' => __('Status 1'),'2' => __('Status 2'),'-1' => __('Status -1')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function admin()
    {
        return $this->hasOne('Admin', 'id', 'admin_id', [], 'LEFT')->field('id,username,nickname');
    }

    public function user()
    {
        return $this->hasOne('User', 'id', 'user_id', [], 'LEFT')->field('id,username,nickname');
    }

    public function userbank()
    {
        return $this->hasOne('Bank', 'id', 'bank_id', [], 'LEFT')->field('id,name,bankname,banklocation,bankcard');
    }

    public function banktext()
    {
        return $this->hasOne('Bank', 'id', 'bank_id', [], 'LEFT')->field('id,CONCAT(bankname,\'（\',right(bankcard, 4) ,\'） \',`name`) as text');
    }


    /*public function admin()
    {
        return $this->belongsTo('Admin', 'admin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function bank()
    {
        return $this->belongsTo('Bank','bank_id','id',[],'LEFT')->setEagerlyType(0);
    }*/


}
