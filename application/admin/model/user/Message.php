<?php

namespace app\admin\model\user;

use think\Model;
use traits\model\SoftDelete;

class Message extends Model
{
    // 表名
    protected $name = 'user_message';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    use SoftDelete;
    protected $deleteTime = 'delete_time';

    
    // 追加属性
    protected $append = [
        'status_text',
    ];
    

    
    public function getStatusList()
    {
        return ['0' => __('Status 0'),'1' => __('Status 1')];
    }     


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }







}
