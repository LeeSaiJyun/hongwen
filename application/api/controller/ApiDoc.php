<?php
/**
 * Created by PhpStorm.
 * User: mao
 * Date: 18-12-5
 * Time: 上午11:15
 */

namespace app\api\controller;


use ReflectionClass;
use ReflectionMethod;

class ApiDoc extends ApiAbstractController {

	private $apiList = [
		"\\app\\api\\controller\\WeChatMember",
		"\\app\\api\\controller\\MemberAddress",
		"\\app\\api\\controller\\MemberBank",
		"\\app\\api\\controller\\SmsAuth",
		"\\app\\api\\controller\\Reservation",
		"\\app\\api\\controller\\Application",
		"\\app\\api\\controller\\DeliveryMaterial",
		"\\app\\api\\controller\\Order",
		"\\app\\api\\controller\\school\\Cat",
		"\\app\\api\\controller\\school\\School",
		"\\app\\api\\controller\\school\\Major",
		"\\app\\api\\controller\\User",
		"\\app\\api\\controller\\Withdraw",
		"\\app\\api\\controller\\MemberMessage",
	];

	/**
	 * @return \think\Response
	 * @throws \ReflectionException
	 */
	public function getList() {
		$match = null;
		$result = [];
		$token = isset(P()["token"]) ? P("token") : "YmM3YjYxY2ZjZTkzNTUyOTQxNjM2OTlmOWQ1YzI3NjU2MTIzOQ==";
		foreach ($this->apiList as $k => $v) {
			$rl = new ReflectionClass($v);
			$methods = $rl->getMethods(ReflectionMethod::IS_PUBLIC);
			$apiUrlPrefix = $rl->getConstant("API_URL");
			foreach ($methods as $k2 => $v2) {
				$doc = $v2->getDocComment();
				preg_match_all('/@label.*?/U', $doc, $match);
				if (count($match[0]) > 0) {
					$result[$apiUrlPrefix."/".$v2->getName()] = [];
					$result[$apiUrlPrefix."/".$v2->getName()]["label"] = trim(explode("@label ", $match[0][0])[1]);
					$result[$apiUrlPrefix."/".$v2->getName()]["testing"] = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].  $apiUrlPrefix."/".$v2->getName()."?token={$token}";
					preg_match_all('/@param.*?/U', $doc, $match);
					if (count($match[0]) > 0) {
						$result[$apiUrlPrefix."/".$v2->getName()]["params"] = [];
						foreach ($match[0] as $k3 => $v3)
							$result[$apiUrlPrefix."/".$v2->getName()]["params"][] = trim(explode("@param ", $v3)[1]);
					}
				}
			}
		}
		return $this->success($result);
	}

}
