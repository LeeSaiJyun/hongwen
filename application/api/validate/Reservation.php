<?php

namespace app\api\validate;

use think\Validate;

class Reservation extends Validate
{

    //手机号正则表达式
    protected $regex = ['mobile' => "^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\\d{8}$"];

    /**
     * 验证规则
     */
    protected $rule = [
        'telephone' => 'require|mobile',
        'name' => 'require|max:50',
        'appointedtime' => 'require|date',

    ];

    /**
     * 提示消息
     */
    protected $message = [
        'telephone.require' => '请输入手机号',
        'telephone.mobile' => '请输入正确的手机号',
        'name.require' => '请输入姓名',
        'appointedtime.require' => '请输入预约时间',
        'appointedtime.date' => '请输入一个有效的日期或时间格式',
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['name','telephone','appointedtime'],
        'edit' => ['name','telephone','appointedtime'],
    ];


}
