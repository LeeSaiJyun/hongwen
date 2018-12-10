<?php

namespace app\api\validate;

use think\Validate;

class Order extends Validate{

    /**
     * 验证规则
     */
    protected $rule = [
        'money|金额' => 'require|number|>=:1',
        'paymentdata|类型'  => 'require'
    ];

    /**
     * 提示消息
     */
    protected $message = [
    ];
    /**
     * 验证场景
     */
    protected $scene = [
    ];


}
