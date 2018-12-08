<?php

namespace app\api\controller;

use app\admin\model\UserAddress;
use app\common\controller\Api;
use think\Request;

/**
 * 教材邮寄
 * @author leesaijyun
 */
class DeliveryMaterial extends Api
{
    protected $model = null;

    protected $noNeedLogin = [''];
    protected $noNeedRight = '*';

    public function _initialize(){
        parent::_initialize();
        $this->model = new \app\api\model\DeliveryMaterial();
    }

    //添加邮寄
    public function create(Request $request){

        $delivery_data = $request->param();
        $delivery_data['user_id'] = $this->auth->id?:1;//用户ID


        $validate = validate('DeliveryMaterial');
        if(!$validate->check($delivery_data)){
            $this->error($validate->getError());
        }

        //查询地址
        $where =[
            'id' => $delivery_data['user_address_id'],
            'user_id' => $this->auth->id,
        ];
        $result = UserAddress::field(['id','address','full_address'])->where($where)->find();
        $delivery_data['address'] = $result['address'];
        $delivery_data['full_address'] = $result['full_address'];
        if(!$result){
            $this->error('所选地址不存在',$result);
        }

        $this->model->allowField(['application_id','user_address_id','remark','address','full_address','status'])->save($delivery_data);
        $this->success('success');
    }

}
