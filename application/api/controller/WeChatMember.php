<?php
/**
 * Created by PhpStorm.
 * User: mao
 * Date: 18-12-4
 * Time: 上午9:56
 */

namespace app\api\controller;


use app\admin\model\User;
use app\admin\model\user\Token;
use app\api\library\WxPay\WxPayTrue;
use app\api\model\WeChatMember as WeChatModel;
use think\Config;
use think\Db;
use think\Exception;
use think\Response;

class WeChatMember extends ApiAbstractController {

	const API_URL = "/api/weChatMember";

	/**
	 * @label 微信登录、注册操作，并返回token
	 * @param openId:openId
	 * @param nickName:微信昵称
	 * @param avatarUrl:微信头像
	 * @param gender:性别
	 * @return \think\Response
	 */
	public function signByWeChat() {
		return $this->tryCatchTx(function () {
			// 1，没有token的就判断openid是否存在，则注册操作
			// 2，进行登录更新token操作
			checkParams([
				"openId"    => "openId",
				"nickName"  => "微信昵称",
				"avatarUrl" => "微信头像",
				"gender"    => "性别",
			]);
			$openID = P("openId");
			F(strlen($openID) != 28, "openId不合法");
			// 判断openid是否已经存在，是则登录操作，否则注册并登录操作
			$userModel = new User();
			$member    = $userModel->where(["openid" => $openID])->find();
			$salt      = rand(100000, 999999);
			$add       = [
				"openid"   => $openID,
				"nickname" => P("nickName"),
				"gender"   => P("gender"),
				"avatar"   => P("avatarUrl"),
				"salt"     => $salt,
				"password" => md5(rand(100000, 999999) . $salt),
				"createtime" => time(),
				"jointime" => time(),
			];
			// 判断是否存在邀请码
			if (isset(P()["pid"])) {
				$parent = $userModel->where(["openid" => P("pid")])->find();
				if ($parent) {
					$add["pid"]  = $parent["id"];
					$add["pids"] = $parent["pids"] . ($parent["pids"] ? "," : "") . $parent["id"];
				}
			}
			$uid      = $member ? $member["id"] : $userModel->insert($add, FALSE, TRUE);
			$tokenLog = Token::get(["user_id" => $uid]);
			$token    = base64_encode(md5(time() . $uid) . rand(10000, 99999));
			if ($tokenLog) {
				Token::update(["expiretime" => time() + 86400 * 10], ["token" => $tokenLog["token"]]);
				$userModel->save([
					"nickname" => P("nickName"),
					"avatar" => P("avatarUrl"),
				], ["id" => $uid]);
			} else Token::create([
				"user_id"    => $uid,
				"token"      => $token,
				"createtime" => time(),
				"expiretime" => time() + 86400 * 10,
			]);
			return $tokenLog ? $tokenLog["token"] : $token;
		});
	}

	/**
	 * @label 检测用户token合法性
	 * @return \think\Response
	 */
	public function checkToken() {
		return $this->tryCatch(function () {
			return is_bool(WeChatModel::checkToken(P()));
		});
	}

	/**
	 * @label 获取我自己信息
	 * @return \think\Response
	 */
	public function getMe() {
		return $this->tryCatch(function () {
			$tokenData = WeChatModel::checkToken(P());
			return User::get(["id" => $tokenData["user_id"]]);
		});
	}

	/**
	 * @label 退出操作
	 * @return \think\Response
	 */
	public function signOut() {
		return $this->tryCatchTx(function () {
			$tokenData = WeChatModel::checkToken(P());
			return Token::destroy("token='{$tokenData["token"]}'");
		});
	}

	/**
	 * @label 获取用户所属下级用户列表
	 * @return \think\Response
	 */
		public function getSubMemberList() {
		return $this->tryCatch(function () {
			$tokenData = WeChatModel::checkToken(P());
			$model     = new User();
            $submember = $model->alias('u')
                ->field('u.id,u.nickname,u.realname,u.mobile,from_unixtime(u.jointime) as jointime, 
                g.name as grade,t.type_name as type,s.name as school,c.name as cat,m.name as major')
                ->where(["pid" => $tokenData["user_id"]])
                ->join('fa_grade g','u.grade_id = g.id','LEFT')			//年级
                ->join('fa_school_type t','u.type_id = t.id','LEFT')		//类型
                ->join('fa_school s','u.school_id = s.id','LEFT')		//学校
                ->join('fa_school_cat_access c_acc','u.cat_access_id = c_acc.id','LEFT')  //cat_access
				->join('fa_school_cat c','c_acc.school_cat_id = c.id','LEFT')	//层次
                ->join('fa_school_major m','u.major_id = m.id','LEFT')			//专业
                ->select();

            foreach ($submember as $row => &$item) {
                $applicationtime = \app\api\model\Application::where(['user_id' => $item['id']])->order("id desc")->value('applicationtime');
                $item['applicationtime'] =  $applicationtime?date('Y-m-d H:i:s',$applicationtime):null;
            }
            return $submember;
		});
	}

	/**
	 * @label 获取小程序码
	 * @param openid
	 * @return \think\Response
	 * @throws \Exception
	 */
	public function getWXACode($openid =null) {
		$url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit";
		$params = [
			"scene" => input("openid"),
			"width" => 280
//			"page" => "page/index"
		];
		$qrcode =  $this->wxCURL($url,json_encode($params)) ;
		return new Response( $qrcode, 200, ['Content-Type' => 'image/jpg']);
	}

}
