<?php
/**
 * Created by PhpStorm.
 * User: mao
 * Date: 18-12-5
 * Time: 下午2:47
 */

namespace app\api\controller;


use app\admin\model\Area;
use app\admin\model\user\Address;
use app\api\model\WeChatMember;

class MemberAddress extends ApiAbstractController {

	const API_URL = "/api/memberAddress";

	/**
	 * @label 获取省市区地址列表
	 * @return \think\Response
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function getArea() {
		$model  = new Area();
		$data   = $model->field(["id", "name", "pid", "level"])->select();
		$result = [];
		$level3 = [];
		foreach ($data as $k => $v) {
			if ($v["pid"] == "0") $result[$v["id"]] = [
				"id"    => $v["id"],
				"pid"   => $v["pid"],
				"name"  => $v["name"],
				"level" => $v["level"],
				"items" => []];
			else {
				$v["level"] == "2" && $result[$v["pid"]]["items"][$v["id"]] = [
					"id"    => $v["id"],
					"pid"   => $v["pid"],
					"name"  => $v["name"],
					"level" => $v["level"],
					"items" => []];
				$v["level"] == "3" && array_push($level3, $v);
			}
		}
		foreach ($result as $k => $v)
			foreach ($v["items"] as $k2 => $v2)
				foreach ($level3 as $k3 => $v3)
					if ($v2["id"] == $v3["pid"]) {
						array_push($result[$k]["items"][$k2]["items"], $v3);
						unset($level3[$k3]);
					}
		return $this->success(array_values($result));
	}

	/**
	 * @label 获取用户收货地址列表
	 * @return \think\Response
	 */
	public function getList() {
		return $this->tryCatch(function () {
			$tokenData = WeChatMember::checkToken(P());
			$data = Address::all(["user_id" => $tokenData["user_id"]]);
			if (count($data) > 0)
				foreach ($data as $k => $v)
					$data[$k]["telephone"] = mb_substr($v["telephone"], 0, 3,"utf-8")."*****".mb_substr($v["telephone"], -3, 3,"utf-8");
			return $data;
		});
	}

	/**
	 * @label 获取用户指定ID的收货地址数据
	 * @param id:地址ID
	 * @return \think\Response
	 */
	public function getOne() {
		return $this->tryCatch(function () {
			$tokenData = WeChatMember::checkToken(P());
			checkParams([
				"id" => "地址ID",
			]);
			return Address::get(["user_id" => $tokenData["user_id"], "id" => P("id")]);
		});
	}

	/**
	 * @label 编辑用户收货地址
	 * @param id:地址ID
	 * @param province_id:省份ID
	 * @param city_id:城市ID
	 * @param area_id:地区ID
	 * @param name:收货人姓名
	 * @param telephone:收货人联系电话
	 * @param address:收货人详细地址
	 * @return \think\Response
	 */
	public function modify() {
		return $this->tryCatchTx(function () {
			$tokenData = WeChatMember::checkToken(P());
			checkParams([
				"id"          => "地址ID",
				"province_id" => "省份名称",
				"city_id"     => "城市名称",
				"area_id"     => "地区名称",
				"name"        => "收货人姓名",
				"telephone"   => "收货人联系电话",
				"address"     => "收货人详细地址",
			]);
			!is_numeric(P("telephone")) && E("这是手机号？");
			$addressData = Address::get(["user_id" => $tokenData["user_id"], "id" => P("id")]);
			!$addressData && E("收货地址不是你的");
			Address::update([
				"province_id" => P("province_id"),
				"city_id" => P("city_id"),
				"area_id" => P("area_id"),
				"name" => P("name"),
				"telephone" => P("telephone"),
				"address" => P("address"),
				"full_address" => P("province_id").P("city_id").P("area_id").P("address"),
			], ["id" => $addressData["id"]]);
		});
	}

	/**
	 * @label 创建用户收货地址
	 * @param province_id:省份ID
	 * @param city_id:城市ID
	 * @param area_id:地区ID
	 * @param name:收货人姓名
	 * @param telephone:收货人联系电话
	 * @param address:收货人详细地址
	 * @return \think\Response
	 */
	public function create() {
		return $this->tryCatchTx(function () {
			$tokenData = WeChatMember::checkToken(P());
			checkParams([
				"province_id" => "省份名称",
				"city_id"     => "城市名称",
				"area_id"     => "地区名称",
				"name"        => "收货人姓名",
				"telephone"   => "收货人联系电话",
				"address"     => "收货人详细地址",
			]);
			!is_numeric(P("telephone")) && E("这是手机号？");
			Address::create([
				"province_id" => P("province_id"),
				"city_id" => P("city_id"),
				"area_id" => P("area_id"),
				"name" => P("name"),
				"telephone" => P("telephone"),
				"address" => P("address"),
				"full_address" => P("province_id").P("city_id").P("area_id").P("address"),
				"user_id" => $tokenData["user_id"],
			]);
		});
	}

	/**
	 * @label 用户删除收货地址
	 * @return \think\Response
	 */
	public function delete() {
		return $this->tryCatchTx(function(){
			$tokenData = WeChatMember::checkToken(P());
			checkParams([
				"id" => "地址ID",
			]);
			Address::destroy(["id" => P("id"), "user_id" => $tokenData["user_id"]]);
		});
	}

}
