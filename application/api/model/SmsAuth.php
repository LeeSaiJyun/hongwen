<?php
/**
 * Created by PhpStorm.
 * User: mao
 * Date: 18-11-22
 * Time: 下午1:53
 */

namespace app\api\model;


use think\Model;

/**
 * 华信短信 - 模型
 * Class SmsAuth
 * @package app\config\model
 */
class SmsAuth extends Model {

	protected $table = "bb_sms_auth";

	/**
	 * 删除所有过期的短信信息
	 */
	public function clearExpiredSms() {
		$this->where("expire_time < ".time())->delete();
	}

    /**
     * 检测认证码是否正确
     * @param $mobile
     * @param $code
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
	public static function checkAuth($mobile, $code) {
	    $data = self::all(["mobile" => $mobile, "code" => $code]);
	    $len = count($data);
		F($len < 1, "验证码不存在");
		F($data[$len-1]["expire_time"] < time(), "验证码已过期");
		// 认证码正确的话，就删除此记录
		self::destroy(["id" => $data[$len-1]["id"]]);
	}

	/**
	 * 根据给出的手机号发送短信
	 * @param $mobile
	 * @return bool|string
	 */
	public static function sendMessage($mobile) {
		$code = rand(100000,999999);
		// 发送阿里云短信
		//\app\api\library\Aliyun\Sms::sendSms($mobile, $code);
		// 插入到数据库中
		self::create([
			"mobile" => $mobile,
			"code" => $code,
			"create_time" => time(),
			"expire_time" => time()+1800,
		]);
        return $code;
	}

}
