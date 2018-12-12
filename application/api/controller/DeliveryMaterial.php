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
	const API_URL = "/api/deliveryMaterial";
    protected $model = null;

    protected $noNeedLogin = [''];
    protected $noNeedRight = '*';

    public function _initialize(){
        parent::_initialize();
        $this->model = new \app\api\model\DeliveryMaterial();
    }

	/**
	 * @label 添加邮寄
     * @param application_id:报名申请ID
     * @param user_address_id:用户地址ID
     * @param remark:备注
     */
    public function create(Request $request){

        $delivery_data = $request->param();
        $delivery_data['user_id'] = $this->auth->id?:1;//用户ID


        $validate = validate('DeliveryMaterial');
        if(!$validate->check($delivery_data)){
            $this->error($validate->getError());
        }
        $app = \app\api\model\Application::get(['id'=>$delivery_data['application_id'],'user_id'=>$this->auth->id]);
        if(!$app){
            $this->error('报名不存在');
        }
        //查询地址
        $where =[
            'id' => $delivery_data['user_address_id'],
            'user_id' => $this->auth->id,
        ];
        $result = UserAddress::field(['id','address','name','telephone','full_address'])->where($where)->find();
        $delivery_data['address'] = $result['address'];
        $delivery_data['name'] = $result['name'];
        $delivery_data['telephone'] = $result['telephone'];
        $delivery_data['full_address'] = $result['full_address'];
        if(!$result){
            $this->error('所选地址不存在',$result);
        }

        $delivery_data['status'] = 0;

        $this->model->allowField(['application_id','user_address_id','name','telephone','remark','address','full_address','status'])->save($delivery_data);
        $this->success('success');
    }

}
