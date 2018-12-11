<?php

namespace app\api\validate;

use think\Validate;

class Application extends Validate
{

    //手机号正则表达式
    protected $regex = ['mobile' => "^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\\d{8}$"];

    /**
     * 验证规则
     */
    protected $rule = [
        'name|姓名' => 'require|max:50',
        'telephone|手机号' => 'require|mobile',
        'idcard|身份证号' => 'require',
        'birthday|生日' => 'require|date',
        'sex|性别' => 'require',
        'ethnic|民族' => 'require',
        'graduation|最高学历学校' => 'require',
        'certificate|证书编号' => 'require',
        'graduationdate|毕业时间' => 'require',
        'graduationmajor|毕业专业' => 'require',
        'school_id' => 'require',
        'major_id' => 'require',
    ];

    /**
     * 提示消息
     */
    protected $message = [
        'telephone.mobile' => '请输入正确的手机号',
        'school_id.require' => '请选择报名学校',
        'major_id.require' => '请选择报名专业',
    ];

    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['name','telephone','appointedtime'],
        'edit' => ['name','telephone','appointedtime'],
    ];


}