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
		return $this->success($data ? $data : "成功");
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
		return $this->success($data ? $data : "成功");
	}

}
