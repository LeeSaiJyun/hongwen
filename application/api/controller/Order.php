<?php

namespace app\api\controller;

use app\api\library\WxPay\WxPay;
use app\common\controller\Api;
use think\Db;
use think\Request;

/**
 * 订单
 * @author leesaijyun
 */
class Order extends Api
{

	const  API_URL = "/api/order";

    protected $model = null;

    protected $noNeedLogin = [''];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\api\model\Order();
    }


    /**
     * @label 报名订单
     */
    public function createApplication(){
        $data['user_id'] = $this->auth->id;
        $data['paymentdata'] = 'application'; // application=>报名费,tuition=>学费
        $data['money'] = \think\Config::get("site.registration_fee");//固定报名费用

        //数据校验
        $validate = validate('Order');
        if(!$validate->check($data)){
            $this->error($validate->getError());
        }

        $result = $this->model->createOrder($data['user_id'],$data['money'],$data['paymentdata']);
        $this->success($result);
    }

    /**
     * @label 学费订单
     * @param money:金额
     */
    public function createTuition(Request $request){
        $data['user_id'] = $this->auth->id;
        $data['paymentdata'] = 'tuition'; // application=>报名费,tuition=>学费
        $data['money'] = $request->param('money');



        //数据校验
        $validate = validate('Order');
        if(!$validate->check($data)){
            $this->error($validate->getError());
        }

        $result = $this->model->createOrder($data['user_id'],$data['money'],$data['paymentdata']);
	    // 加入微信统一下单
	    $member = \app\admin\model\User::get(["id" => $data["user_id"]]);
	    $this->success(WxPay::createWxPayUnifiedOrder($member["openid"], $result["orderno"], $result["money"]));
    }

    public function createApplicationWxUnifiedOrder(Request $request) {

    }

}
