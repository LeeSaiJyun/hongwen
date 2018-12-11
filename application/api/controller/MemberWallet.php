<?php
/**
 * Created by PhpStorm.
 * User: mao
 * Date: 18-12-6
 * Time: 上午10:19
 */

namespace app\api\controller;


use app\admin\model\Bank;
use app\admin\model\User;
use app\admin\model\Withdraw;
use app\api\model\PayLog;
use app\api\model\WeChatMember;

class MemberWallet extends ApiAbstractController {

	const API_URL="/api/memberWallet";

	/**
	 * @label 申请提现
	 * @param bank_id:银行卡ID
	 * @param money:申请提现的金额
	 * @return \think\Response
	 */
	public function applyWithdraw() {
		return $this->tryCatchTx(function(){
			checkParams([
				"bank_id" => "银行卡ID",
				"money" => "申请提现的金额",
			]);
			$tokenData = WeChatMember::checkToken(P());
			$bankData = Bank::get(["id" => P("bank_id")]);
			!$bankData && E("你没有此银行卡");
			$model = new Withdraw();
			$withdrawData = $model->order("id desc")->where(["user_id" => $tokenData["user_id"]])->find();
			($withdrawData && $withdrawData["status"] == "0") && E("你已申请过，请等待管理员处理");
			$me = User::get(["id" => $tokenData["user_id"]]);
			$me["balance"] < P("money") && E("你没有那么多钱可以提现啊");
			$model->insert([
				"bank_id" => P("bank_id"),
				"money" => abs(round(P("money"), 2)),
				"user_id" => $tokenData["user_id"],
				"withdrawtime" => time(),
			]);
			User::update([
				"balance" => abs(round($me["balance"]-P("money"), 2)),
				"frozen" => abs(round($me["frozen"]+P("money"), 2)),
			], ["id" => $tokenData["user_id"]]);
		});
	}

	/**
	 * @label 创建临时支付记录表 ===== 微信支付前调用此方法
	 * @param money:金额
	 * @param pay_type:支付类型（缴费，报名）
	 * @param source_order_no:源订单号
	 * @return \think\Response
	 */
	public function createPayLog() {
		return $this->tryCatchTx(function (){
			$tokenData = WeChatMember::checkToken(P());
			checkParams(["money" => "金额", "pay_type" => "支付类型", "source_order_no" => "源订单号"]);
			$orderNo = PayLog::createOrderNo();
			PayLog::create([
				"uid" => $tokenData["user_id"],
				"money" => abs(round(P("money"), 2)),
				"create_time" => time(),
				"order_no" => $orderNo,
				"source_order_no" => P("source_order_no"),
			]);
			return $orderNo;
		});
	}

}
