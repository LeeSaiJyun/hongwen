<?php
/**
 * Created by PhpStorm.
 * User: mao
 * Date: 18-12-5
 * Time: 下午4:23
 */

namespace app\api\controller;


use app\admin\model\Bank;
use app\api\model\WeChatMember;

class MemberBank extends ApiAbstractController {

	const API_URL = "/api/memberBank";

	/**
	 * @label 获取用户银行卡列表
	 * @return \think\Response
	 */
	public function getList() {
		return $this->tryCatch(function(){
			$tokenData = WeChatMember::checkToken(P());
			$data = Bank::all(["user_id" => $tokenData["user_id"]]);
			foreach ($data as $k => $v)
				$data[$k]["bankcard"] = "**** **** **** ".substr($v["bankcard"],-4);
			return $data;
		});
	}

	/**
	 * @label 获取用户银行卡指定ID明细
	 * @param id:银行卡ID
	 * @return \think\Response
	 */
	public function getOne() {
		return $this->tryCatch(function(){
			checkParams(["id" => "银行卡ID"]);
			$tokenData = WeChatMember::checkToken(P());
			$data = Bank::get(["user_id" => $tokenData["user_id"], "id" => P("id")]);
			return $data ?? [];
		});
	}

	/**
	 * @label 创建用户银行卡
	 * @param name:开户姓名
	 * @param bankname:银行名称
	 * @param bankcard:银行卡号
	 * @return \think\Response
	 */
	public function create() {
		return $this->tryCatchTx(function(){
			$tokenData = WeChatMember::checkToken(P());
			checkParams([
				"name" => "开户姓名",
				"bankname" => "银行名称",
				"bankcard" => "银行卡号",
			]);
			Bank::create([
				"name" => P("name"),
				"bankname" => P("bankname"),
				"bankcard" => P("bankcard"),
				"user_id" => $tokenData["user_id"],
			]);
		});
	}

	/**
	 * @label 用户编辑银行卡信息
	 * @param id:银行卡ID
	 * @param name:开户姓名
	 * @param bankname:银行名称
	 * @param bankcard:银行卡号
	 * @return \think\Response
	 */
	public function modify() {
		return $this->tryCatchTx(function(){
			$tokenData = WeChatMember::checkToken(P());
			checkParams([
				"id" => "银行卡ID",
				"name" => "开户姓名",
				"bankname" => "银行名称",
				"bankcard" => "银行卡号",
			]);
			$cardData = Bank::get(["id" => P("id"), "user_id" => $tokenData["user_id"]]);
			!$cardData && E("银行卡信息不存在");
			Bank::create([
				"name" => P("name"),
				"bankname" => P("bankname"),
				"bankcard" => P("bankcard"),
			], ["id" => $cardData["id"]]);
		});
	}

	/**
	 * @label 用户删除绑定银行卡
	 * @param id:银行卡ID
	 * @return \think\Response
	 */
	public function delete() {
		return $this->tryCatchTx(function(){
			checkParams(["id" => "银行卡ID"]);
			$tokenData = WeChatMember::checkToken(P());
			$cardData = Bank::get(["id" => P("id"), "user_id" => $tokenData["user_id"]]);
			!$cardData && E("银行卡信息不存在");
			Bank::destroy("id={$cardData['id']}");
		});
	}

}
