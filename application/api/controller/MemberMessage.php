<?php
/**
 * Created by PhpStorm.
 * User: mao
 * Date: 18-12-6
 * Time: 上午9:37
 */

namespace app\api\controller;
use app\admin\model\user\Message;
use app\api\model\WeChatMember;

class MemberMessage extends ApiAbstractController {

	const API_URL="/api/memberMessage";

	/**
	 * @label 获取用户消息列表
	 * @return \think\Response
	 */
	public function getMyMessage() {
		return $this->tryCatch(function(){
			$tokeData = WeChatMember::checkToken(P());
			$data = Message::all(["user_ids" => $tokeData["user_id"]]);
			foreach ($data as $k => $v) {
				$timestamp = $v["createtime"];
				$data[$k]["createtime"] = date("Y-m-d H:i:s", $timestamp);
				$data[$k]["updatetime"] = date("Y-m-d H:i:s", $v["updatetime"]);
				$data[$k]["date_label"] = date("m月d日", $timestamp);
			}
			return $data;
		});
	}

	/**
	 * @label 阅读消息
	 * @param id:消息ID
	 * @return \think\Response
	 */
	public function read() {
		return $this->tryCatchTx(function(){
			checkParams(["id" => "消息ID"]);
			$tokeData = WeChatMember::checkToken(P());
			Message::update([
				"updatetime" => time(),
			], ["id" => P("id")]);
		});
	}

}
