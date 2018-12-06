<?php

namespace app\api\validate;

use think\Validate;

class DeliveryMaterial extends Validate
{

    /**
     * 验证规则
     */
    protected $rule = [
        'application_id' => 'require',
        'user_address_id' => 'require',
    ];

    /**
     * 提示消息
     */
    protected $message = [
        'application_id.require' => '报名ID不存在',
        'user_address_id.require' => '请选择邮寄地址',
    ];

    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['application_id'],
    ];


}
