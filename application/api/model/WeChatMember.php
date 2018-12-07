<?php
/**
 * Created by PhpStorm.
 * User: mao
 * Date: 18-12-5
 * Time: 上午11:04
 */

namespace app\api\model;


use app\admin\model\user\Token;

class WeChatMember {

	/**
	 * 检测token
	 * @param $params
	 * @return mixed
	 * @throws \Exception
	 */
	public static function checkToken($params) {
		F(!$params["token"], "token丢失");
		$data = Token::get(["token" => $params["token"]]);
		!$data && E("token 不存在");
		$data["expiretime"] < time() && E("认证已过期");
		return $data;
	}

}
