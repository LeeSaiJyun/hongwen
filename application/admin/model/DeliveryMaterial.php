<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class DeliveryMaterial extends Model
{
    // 表名
    protected $name = 'delivery_material';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    use SoftDelete;
    protected $deleteTime = 'delete_time';
    
    // 追加属性
    protected $append = [
        'status_text'
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




    /*public function application(){
        return $this->belongsTo('Application', 'application_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }*/

    public function application()
    {
//        return $this->hasOne('Application', 'id', 'application_id', [], 'LEFT')->field('id,telephone');
        return $this->hasOne('Application', 'id', 'application_id', [], 'LEFT')->setEagerlyType(0);
    }

}
