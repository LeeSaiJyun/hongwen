<?php
/**
 * Created by PhpStorm.
 * User: mao
 * Date: 18-12-14
 * Time: 上午9:50
 */

namespace app\api\library\WxPay;


class WxPayTrue {

	private static $appid = "wx20b7656e2486b6e3";
	private static $sub_appid = "wx80d1c1ee6d182609";
	private static $mp_secret = "58df62fa1d447ac39782f5388b8c6c0d";
	private static $mch_id = "1351283901";
	private static $sub_mch_id = "1520342181";
	private static $notify_url = "https://xy.gdhwjyedu.com/api/order/createApplicationWxUnifiedOrder";
	private static $secretKey = "GwJAXR1G0seGKVhgXVMMJJXInKnyIOkr";
	private static $signType = "MD5";

	//获取用户IP地址
	public static function GetClientIP() {
		if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
			$ip = getenv('REMOTE_ADDR');
		} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
	}

	public static function SortDicXml($strDic,$keySign){
		ksort($strDic);
		$tmp='';
		foreach ($strDic as $key => $value)
			$tmp.="<".$key.">".$value."</".$key.">";
		$sortXml="<xml>".$tmp."<sign>".$keySign."</sign></xml>";
		return $sortXml;
	}

	public static function SortDic(array $strDic){
		//排序
		ksort($strDic);
		//排列成键值对
		$sortDic='';
		foreach ($strDic as $key => $value) {
			if($value==''){
				continue;
			}
			$sortDic.=$key."=".$value."&";
		}
		$sortDic.="key=".self::$secretKey;
		return $sortDic;
	}

	public static function MD5Upper($str){
		return strtoupper(md5($str));
	}

	/**
	 * 使用微信预支付信息生成JSApi提交所需JSon数据
	 * @param string $nonce_str 预支付随机字串
	 * @param string $prepay_id 预支付交易标识
	 * @return array
	 */
	public static function CreateJSApi($nonce_str,$prepay_id){
		$jsapi=array(
			"appId"=>self::$sub_appid,
			"timeStamp"=> time()."",
			"nonceStr"=>$nonce_str,
			"package"=>"prepay_id=".$prepay_id,
			"signType"=>self::$signType,
		);
		$jsapi['paySign']= self::MD5Upper(self::SortDic($jsapi));
		return $jsapi;
	}

	public static function createWxPayUnifiedOrder($openID, $orderNo, $money, $product='学费支付') {
		$wcMutually = new WeChatMutually();
		$rand = self::MD5Upper(time());
		$structOrder = [
			"appid"=>self::$appid,
			"sub_appid"=>self::$sub_appid,
			"sub_mch_id"=>self::$sub_mch_id,
			"mch_id"=>self::$mch_id,
			"out_trade_no"=>$orderNo,
			"spbill_create_ip"=>self::GetClientIP(),//$order['userIP'],
			"notify_url"=>self::$notify_url,
			"trade_type"=>"JSAPI",
			"total_fee"=>round($money*100, 2)."",
			"body"=>$product,
			"sub_openid"=>$openID,
			//"openid"=>$openID,
			"nonce_str"=>$rand,
			"sign_type" => "MD5",
		];
		//过滤空值
		$structOrder=array_filter($structOrder);
		//对字符进行排序并加密钥
		//生成加密签名
		//排序并生成xml
		$preXml = self::SortDicXml($structOrder,self::MD5Upper(self::SortDic($structOrder)));
		//提交预支付信息
		$postXml = $wcMutually->PostCurl('https://api.mch.weixin.qq.com/pay/unifiedorder',$preXml);
		//获取预支付结果
		$result = $wcMutually->XmlToArray($postXml);
		if($wcMutually->XmlStr($result,"return_code")=='FAIL'){
			$msg=$wcMutually->XmlStr($result,"return_msg");
			return ['status'=>false,'msg'=>'调起微信支付失败','data'=>$msg];
		}
		//提取支付参数
		$nonce_str=$wcMutually->XmlStr($result,"nonce_str");
		$prepay_id=$wcMutually->XmlStr($result,"prepay_id");
		//返回前台JS支付数据
		return ['status'=>true,'msg'=>'准备支付','data'=> self::CreateJSApi($nonce_str,$prepay_id)];
	}

	public static function getWXACode($uid) {
		$wcMutually = new WeChatMutually();
		$get = $wcMutually->GetCurl("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx8c232ae524429b4c&secret=58df62fa1d447ac39782f5388b8c6c0d");
		$get = json_decode($get, true);
		if ($get["access_token"]) {
			$accessToken = $get["access_token"];
			return $wcMutually->PostCurl("https://api.weixin.qq.com/wxa/getwxacodeunlimit", [
					"access_token" => $accessToken,
					"scence" => "pid=".$uid,
			]);
		}
		return false;
	}

}
