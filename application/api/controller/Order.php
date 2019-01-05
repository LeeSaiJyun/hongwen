<?php

namespace app\api\controller;

use app\admin\model\user\Message;
use app\api\library\WxPay\WxPay;
use app\api\library\WxPay\WxPayTrue;
use app\api\model\Test;
use app\common\controller\Api;
use app\common\model\Config;
use think\Db;
use think\Exception;
use think\Request;
use app\admin\model\User as UserModel;

/**
 * 订单
 * @author leesaijyun
 */
class Order extends Api
{

	const  API_URL = "/api/order";

    protected $model = null;

    protected $noNeedLogin = ['createApplicationWxUnifiedOrder'];
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

		$minimum_fee = \think\Config::get("site.minimum_fee");//固定报名费用
		//数据校验
        $validate = validate('Order');
        $rule = [
			'money|金额' => "require|float|>=:$minimum_fee",
			'paymentdata|类型'  => 'require'
		];
        if(!$validate->check($data,$rule)){
            $this->error($validate->getError());
        }

		$result = $this->model->createOrder($data['user_id'],$data['money'],$data['paymentdata']);
	    // 加入微信统一下单
	    $member = \app\admin\model\User::get(["id" => $data["user_id"]]);
	    $this->success(WxPayTrue::createWxPayUnifiedOrder($member["openid"], $result["orderno"], $result["money"]));
	    //$this->success(weChatPay($result["orderno"], $result["money"], $member["openid"]));
	    //$this->success(WxPay::createWxPayUnifiedOrder($member["openid"], $result["orderno"], $result["money"]));
    }

    public function createApplicationWxUnifiedOrder() {
	    $postXml = file_get_contents("php://input");
	    $resultArr = $this->xmlToArray($postXml);
	    if ($resultArr["result_code"] == "SUCCESS") {
		    $money = round($resultArr["total_fee"] / 100, 2);
		    try {
			    $orderModel = new \app\api\model\Order();
			    $order = $orderModel->where(["orderno" => $resultArr["out_trade_no"]])->find();
			    ($money != $order["money"]) && E("金额不对");
			    $orderModel->save([
				    "status" => "1",
				    "paytime" => time(),
			    ], ["orderno" => $resultArr["out_trade_no"]]);

			    $memberModel = new UserModel();
			    $member = $memberModel->where(["id" => $order["user_id"]])->find();
			    $pidArr = explode(",", $member["pids"]);
			    if ($member["pid"] > 0) {
			    	$configModel = new Config();
				    $first  = $configModel->where(["name" => "rebate_percent_1"])->find()["value"];
				    $second = $configModel->where(["name" => "rebate_percent_2"])->find()["value"];
				    // 判断一级分佣是否存在
				    if ($first) {
					    $memberFirst    = $memberModel->where(["id" => $member["pid"]])->find();
					    $currentBalance = round($memberFirst["balance"] + $money * $first / 100, 2);
					    Message::create([
						    "user_ids"        => $member["pid"],
						    "createtime"      => time(),
						    "message_content" => "「{$member['nickname']}」进行支付，返回佣金：￥" . round($money * $first / 100, 2) . "，当前余额：￥" . $currentBalance,
					    ]);
					    $memberModel->save(["balance" => $currentBalance], ["id" => $member["pid"]]);
				    }
				    // 判断二级分佣是否存在和用户上级的上级是否存在
				    if ($second && count($pidArr) > 1) {
					    $memberSecond   = $memberModel->where(["id" => $pidArr[count($pidArr) - 1]])->find();
					    $currentBalance = round($memberSecond["balance"] + $money * $second / 100, 2);
					    Message::create([
						    "user_ids"        => $memberSecond["id"],
						    "createtime"      => time(),
						    "message_content" => "「{$member['nickname']}」进行支付，返回佣金：￥" . round($money * $second / 100, 2) . "，当前余额：￥" . $currentBalance,
					    ]);
					    $memberModel->save(["balance" => $currentBalance], ["id" => $memberSecond["id"]]);
				    }
			    }
		    } catch(\Exception $e) {
			    $this->error($e->getMessage());
			    die;
		    }
		    $this->success("成功");
		    die;
	    }

    }

	private function xmlToArray($xml) {
		//禁止引用外部xml实体
		libxml_disable_entity_loader(true);
		$xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
		$val = json_decode(json_encode($xmlstring), true);
		return $val;
	}

}
