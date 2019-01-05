<?php
/**
 * Created by PhpStorm.
 * User: mao
 * Date: 18-12-4
 * Time: 上午9:57
 */

namespace app\api\controller;


use think\controller\Rest;
use think\Db;
use think\Request;

abstract class ApiAbstractController extends Rest {

	private $noLimit = [
		"api/weChatMember/signByWeChat",
		"api/apiDoc/getList",
		"api/weChatMember/getWXACode"
	];

	/**
	 * 判断除了非限定路由外，其他均要传入token
	 */
	public function __construct() {
		$path = Request::instance()->path();
		$bol1 = !in_array($path, $this->noLimit);
		$bol2 = !isset(P()["token"]) || strlen(P("token")) < 1;
		if (!($bol1 && $bol2)) return parent::__construct();
		die($this->error("Token 丢失")->send());
	}

	/**
	 * 返回成功的json化格式结果
	 * @param $data
	 * @return \think\Response
	 */
	public function success($data) {
		return $this->response([
			"data"     => $data,
			"response" => "ok",
			"code"     => 200,
		], "json", 200);
	}

	/**
	 * 返回失败的json化格式结果
	 * @param $msg
	 * @return \think\Response
	 */
	public function error($msg) {
		return $this->response([
			"data"     => null,
			"response" => $msg,
			"code"     => 400,
		], "json", 400);
	}

	/**
	 * try-catch代码块
	 * @param $func
	 * @return \think\Response
	 */
	protected function tryCatch($func) {
		try {
			$data = $func();
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
		return $this->success(($data || is_array($data)) ? $data : "成功");
	}

	/**
	 * try-catch代码块
	 * @param $func
	 * @return \think\Response
	 */
	protected function tryCatchTx($func) {
		Db::startTrans();
		try {
			$data = $func();
		} catch (\Exception $e) {
			Db::rollback();
			return $this->error($e->getMessage());
		}
		Db::commit();
		return $this->success(($data || is_array($data)) ? $data : "成功");
	}

	/**
	 * 微信请求curl
	 * @param $url
	 * @param $param
	 * @param bool $flag
	 * @return Response
	 */
	public function wxCURL($url, $param, $flag = true) {
		$access_token = $this->getAccessToken();
		if ($flag) {
			$ret = CURL($url . "?access_token=" . $access_token, $param);
		} else {
			$ret = CURL($url, $param);
		}

		if ($ret) {
			$ret_json = json_decode($ret, true);
			//如果是access_token错误则重新获取
			if ($ret_json && array_key_exists('errcode', $ret_json) && $ret_json['errcode'] = 40001) {
				$access_token = $this->getAccessToken(true);    //重新获取access_token
				if ($flag) {
					$ret = CURL($url . "?access_token=" . $access_token, $param);
				} else {
					$ret = CURL($url, $param);
				}

			}
			return $ret;
		}
		return $ret;

	}

	/**
	 * 获取微信access_token
	 * @param $force bool 强制重新获取
	 * @return bool/sting 返回false或token
	 * @throws
	 */
	private function getAccessToken($force = false) {
		$expires_in = Db::name('config')->where('name', 'expires_in')->value('value');

		// ACCESS_TOKEN已过期
		if ($force || (is_numeric($expires_in) && $expires_in <= time())) {
			$appid = \think\Config::get('wechat.sub_appid');
			$appsecret = \think\Config::get('wechat.sub_appsecret');
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
			$output = CURL($url);
			if ($output) {
				$output = json_decode($output, true);
				if (array_key_exists('errcode', $output) && $output['errcode'] != 0) {
					if (array_key_exists('errmsg', $output)) {
						\think\Log::record('微信响应错误', 'error');
						\think\Log::record($output, 'error');
						return false;
					}
				} else {
					if (array_key_exists('access_token', $output)) {
						Db::name('config')->where('name', 'ACCESS_TOKEN')->update(['value' => $output['access_token']]);
						Db::name('config')->where('name', 'expires_in')->update(['value' => time() + $output['expires_in'] - 3600]);
						return $output['access_token'];
					}
				}
			} else {
				\think\Log::record('微信响应错误', 'error');
				\think\Log::record($output, 'error');
				return false;
			}
		} else {
			//未过期
			$ACCESS_TOKEN = Db::name('config')->where('name', 'ACCESS_TOKEN')->value('value');
			return $ACCESS_TOKEN;
		}
	}
}
