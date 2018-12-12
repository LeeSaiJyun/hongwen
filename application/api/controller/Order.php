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

    /**
     * 模型对象
     * @var \think\Model
     */
    protected $model = null;

    protected $noNeedLogin = [''];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\api\model\Order();
    }

    /**
     * @label 创建订单
     * @param money:金额
     * @param type:类型
     */
    public function create(Request $request){
        $data['user_id'] = $this->auth->id;
        $data['paymentdata'] = $request->param('type'); // application=>报名费,tuition=>学费
        if($data['paymentdata'] === 'tuition'){
            $data['money'] = $request->param('money');
        }elseif($data['paymentdata'] === 'application'){
            $data['money'] = \think\Config::get("site.registration_fee");//固定报名费用
        }else{
            $this->error('缴费类型不正确');
        }

        //数据校验
        $validate = validate('Order');
        if(!$validate->check($data)){
            $this->error($validate->getError());
        }

        $result = $this->model->createOrder($data['user_id'],$data['money'],$data['paymentdata']);
        $this->success('success',$result);
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
