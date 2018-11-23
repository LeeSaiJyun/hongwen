<?php

namespace app\admin\validate\user;

use think\Validate;

class Address extends Validate
{

    protected $regex = ['telephone' => "^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\\d{8}$"];

    /**
     * 验证规则
     */
    protected $rule = [
        'telephone' => 'require|telephone',
        'province_id' => 'require|integer',
        'city_id' => 'require|integer',
        'area_id' => 'require|integer',

    ];

    /**
     * 提示消息
     */
    protected $message = [
        'telephone.mobile' => '请输入正确的手机号',
        'province_id.require' => '请选择省',
        'city_id.require' => '请选择市',
        'area_id.require' => '请选择区',
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['telephone','province_id','city_id','area_id'],
        'edit' => ['telephone','province_id','city_id','area_id'],
    ];

    /*public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
//            'telephone'  => __('telephone'),
            'province_id'  => __('province_id'),
            'city_id'  => __('city_id'),
            'area_id' => __('area_id'),
        ];
        parent::__construct($rules, $message, $field);
    }*/

}
