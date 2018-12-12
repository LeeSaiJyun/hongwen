<?php
/**
 * Created by PhpStorm.
 * User: mao
 * Date: 18-11-22
 * Time: 下午2:29
 */

namespace app\api\controller;


use app\api\model\SmsAuth as SmsAuthModel;

class SmsAuth extends ApiAbstractController {

	const API_URL = "/api/smsAuth";
	private $timeLimit = 60;

	/**
	 * @label 获取所有短信认证码 - 仅供后台使用
	 * @return \think\Response
	 * @throws \think\exception\DbException
	 */
	public function getSmsList() {
		$data = SmsAuthModel::all();
		foreach ($data as $k => $v)
			$data[$k]["expire_time_label"] = date("Y-m-d H:i:s", $v["expire_time"]);
		return $this->success($data);
	}

	/**
	 * @label 发送短信验证码动作
	 * @param mobile:手机号码
	 * @return \think\Response
	 */
	public function createNewSms() {
		return $this->tryCatchTx(function(){
			$model = new SmsAuthModel();
			checkParams([
				"mobile" => "手机号码",
			]);
			$model->clearExpiredSms();
			$data = $model->where(["mobile" => P("mobile")])->find();
			F($data && (strtotime($data["create_time"])+$this->timeLimit < time()), "发送验证码太频繁");
			$model->sendMessage(P("mobile"));
		});
	}

}
