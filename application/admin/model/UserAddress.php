<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class UserAddress extends Model
{
    // 表名
    protected $name = 'user_address';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    // 追加属性
    protected $append = [
        'is_default_text'
    ];
    

    public function getIsDefaultList()
    {
        return ['0' => __('Is_default 0'),'1' => __('Is_default 1')];
    }     




    public function getIsDefaultTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['is_default']) ? $data['is_default'] : '');
        $list = $this->getIsDefaultList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    /*public function province()
    {
        return $this->belongsTo('app\admin\model\Area', 'province_id', 'id', [], 'LEFT')->field("id,name")->setEagerlyType(0);
    }

    public function city()
    {
        return $this->belongsTo('app\admin\model\Area', 'city_id', 'id', [], 'LEFT')->field("id,name")->setEagerlyType(0);
    }

    public function area()
    {
        return $this->belongsTo('app\admin\model\Area', 'area_id', 'id', [], 'LEFT')->field("id,name")->setEagerlyType(0);
    }*/


    public function province()
    {
        return $this->hasOne('Area',  'id', 'province_id',[], 'LEFT')->field("id,name");
    }

    public function city()
    {
        return $this->hasOne('Area',  'id', 'city_id',[], 'LEFT')->field("id,name");
    }

    public function area()
    {
        return $this->hasOne('Area',  'id', 'area_id',[], 'LEFT')->field("id,name");
    }



}
