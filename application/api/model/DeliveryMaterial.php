<?php

namespace app\api\model;

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



    

}
