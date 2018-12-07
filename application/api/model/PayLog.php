<?php
/**
 * Created by PhpStorm.
 * User: mao
 * Date: 18-12-6
 * Time: 上午10:44
 */

namespace app\api\model;


use app\admin\model\User;
use app\admin\model\user\Message;
use app\common\model\Config;
use think\Model;

class PayLog extends Model {

	protected $table = "pay_log";

	/**
	 * 创建支付流水号
	 * @return string
	 * @throws \think\exception\DbException
	 */
	public static function createOrderNo() {
		$no = date("YmdHis").rand(100000,999999);
		if (self::get(["order_no" => $no]))
			return self::createOrderNo();
		return $no;
	}

	/**
	 * 更新支付记录并且判断当前用户是否含有2级上级并且进行分佣操作
	 * @param $payLogOrderNo
	 * @param $returnData
	 * @return bool
	 * @throws \think\exception\DbException
	 */
	public static function updatePayLogPayedAndCommission($payLogOrderNo, $returnData, $isCommission = true) {
		$payLogData = self::get(["order_no" => $payLogOrderNo, "status" => -1]);
		!$payLogData && E("支付记录不存在或已经支付完成");
		$member = User::get(["id" => $payLogData["uid"]]);
		!$member && E("用户不存在");
		self::update(["status" => 1, "return_data" => serialize($returnData)], ["id" => $payLogData["id"]]);
		if ($isCommission) {
			$pidArr = explode(",", $member["pids"]);
			if (count($pidArr) > 0) {
				$first = Config::get(["name" => "rebate_percent_1"]);
				$second = Config::get(["name" => "rebate_percent_2"]);
				// 判断一级分佣是否存在
				if ($first) {
					$memberFirst = User::get(["id" => $member["pid"]]);
					Message::create([
						"user_ids" => $member["pid"],
						"createtime" => time(),
						"message_content" => "「{$member['username']}」进行支付，返回佣金：￥".round($payLogData["money"]*$first/100, 2)."，当前余额：￥".round($memberFirst["balance"]+$payLogData["money"]*$first/100, 2),
					]);
					User::update([
						"balance" => round($memberFirst["balance"]+$payLogData["money"]*$first/100, 2),
					], ["id" => $member["pid"]]);
				}
				// 判断二级分佣是否存在和用户上级的上级是否存在
				if ($second && count($pidArr) > 1) {
					$memberSecond = User::get(["id" => $pidArr[count($pidArr)-2]]);
					Message::create([
						"user_ids" => $memberSecond["id"],
						"createtime" => time(),
						"message_content" => "「{$member['username']}」进行支付，返回佣金：￥".round($payLogData["money"]*$first/100, 2)."，当前余额：￥".round($memberFirst["balance"]+$payLogData["money"]*$first/100, 2),
					]);
					User::update([
						"balance" => round($memberSecond["balance"]+$payLogData["money"]*$second/100, 2),
					], ["id" => $memberSecond["id"]]);
				}
			}
		}
		return true;
	}

}
