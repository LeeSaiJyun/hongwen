<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Request;

/**
 * 订单
 * @author leesaijyun
 */
class Order extends Api
{

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
     * 订单
     * @apiMethod    API接口请求方法: POST
     * @apiParam $money string 金额
     * @apiParam $type  string 类型
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
        $this->success($result);
    }

    /**
     * 报名订单
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
     * 学费订单
     * @apiMethod    API接口请求方法: POST
     * @apiParam $money string 金额
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
        $this->success($result);
    }


}
