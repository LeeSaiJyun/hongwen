<?php
/**
 * Created by PhpStorm.
 * User: mao
 * Date: 18-12-7
 * Time: 下午6:03
 */

namespace app\api\library\WxPay;

/*
 *
 * appid=wx20b7656e2486b6e3
sub_appid=wx80d1c1ee6d182609
mch_id=1351283901
sub_mch_id=1520342181
key=GwJAXR1G0seGKVhgXVMMJJXInKnyIOkr

 *
 * */
class WxPay{
	//开发者身份
	private $appid;
	private $sub_appid;
	//商户号
	private $mch_id;
	//子商户号
	private $sub_mch_id;
	//支付回调页面
	private $notify_url;
	//支付密钥
	private $secretKey;
	//加密方式
	private $signType;

	function __construct($wurl=''){
		$wurl=empty($wurl)? '/mall/pay/wcpaysync.html':$wurl;
		$this->appid='wx20b7656e2486b6e3';
		$this->sub_appid='wx80d1c1ee6d182609';
		$this->mch_id = '1351283901';
		$this->sub_mch_id = '1520342181';
		$this->notify_url = 'https://'.$_SERVER['HTTP_HOST'].$wurl;
		$this->secretKey = 'GwJAXR1G0seGKVhgXVMMJJXInKnyIOkr';
		$this->signType = 'MD5';
	}
	/**
	 * 生成预支付定单
	 * @param array $order	商品订单信息数组
	 * @return
	 */
	public function GeneratePrepaidOrder(array $order,$type=0){
		$strDic=array(
			"appid"=>$this->appid,
			"sub_appid"=>$this->sub_appid,
			"mch_id"=>$this->mch_id,
			"out_trade_no"=>$order['out_trade_no'],
			"spbill_create_ip"=>$this->GetClientIP(),//$order['userIP'],
			"notify_url"=>$this->notify_url,
			"trade_type"=>$order['trade_type'],
			"total_fee"=>$order['payAmount'],
			"body"=>$order['body'],
			"detail"=>$order['detail'],
			"sub_openid"=>$order['openId'],
			"nonce_str"=>$this->CreatNonceStr(15,-1),
			"sub_mch_id"=>$this->sub_mch_id,
			"scene_info"=>$order['sceneInfo']
		);
		if($type==1){
			$strDic=array(
				"appid"=>$this->appid,
				"sub_appid"=>$this->sub_appid,
				"mch_id"=>$this->mch_id,
				"out_trade_no"=>$order['out_trade_no'],
				"spbill_create_ip"=>$order['userIP'],
				"notify_url"=>$this->notify_url,
				"trade_type"=>$order['trade_type'],
				"total_fee"=>$order['payAmount'],
				"body"=>$order['body'],
				"detail"=>$order['detail'],
				"sub_openid"=>$order['openId'],
				"nonce_str"=>$this->CreatNonceStr(15,-1),
				"sub_mch_id"=>$this->sub_mch_id,
				"scene_info"=>$order['sceneInfo']
			);
		}
		//过滤空值
		$strDic=array_filter($strDic);
		//对字符进行排序并加密钥
		//生成加密签名
		//排序并生成xml
		return $this->SortDicXml($strDic,$this->MD5Upper($this->SortDic($strDic)));
	}

	/**
	 * 使用微信预支付信息生成JSApi提交所需JSon数据
	 * @param string $nonce_str 预支付随机字串
	 * @param string $prepay_id 预支付交易标识
	 * @return array
	 */
	public function CreateJSApi($nonce_str,$prepay_id){
		$jsapi=array(
			"appId"=>$this->appid,
			"timeStamp"=>(string)time(),
			"nonceStr"=>$nonce_str,
			"package"=>"prepay_id=".$prepay_id,
			"signType"=>$this->signType,
		);
		$jsapi['paySign']=$this->MD5Upper($this->SortDic($jsapi));
		return $jsapi;
	}
	/**
	 * 查询定单信息
	 * @param string $out_trade 定单编号
	 * @return
	 */
	public function QueryOrder($out_trade){
		$reDic=array(
			"appid"=>$this->appid,
			"sub_appid"=>$this->sub_appid,
			"mch_id"=>$this->mch_id,
			"sub_mch_id"=>$this->sub_mch_id,
			"out_trade_no"=>$out_trade,
			"nonce_str"=>$this->CreatNonceStr(15,-1),
		);
		return $this->SortDicXml($reDic,$this->MD5Upper($this->SortDic($reDic)));
	}
	/**
	 * 回复微信支付主动通知
	 */
	public function ReSucces(){
		$sucDic=array(
			"appid"=>$this->appid,
			"sub_appid"=>$this->sub_appid,
			"mch_id"=>$this->mch_id,
			"sub_mch_id"=>$this->sub_mch_id,
			"nonce_str"=>$this->CreatNonceStr(15,-1),
			"return_code"=>"SUCCESS",
			"return_msg"=>"OK",
			"result_code"=>"SUCCESS",
			"err_code_des"=>"OK",
		);
		return $this->SortDicXml($sucDic,$this->MD5Upper($this->SortDic($sucDic)));
	}
	/**
	 * 生成随机字符(默认是5个大小写字母+数字)
	 * @param int $length  要生成的随机字符串长度
	 * @param string $type 随机码类型：
	 * 				0，数字+大小写字母；
	 * 				1，数字；
	 * 				2，小写字母；
	 * 				3，大写字母；
	 * 				4，特殊字符；
	 * 				-1，数字+大小写字母+特殊字符
	 * @return string
	 */
	public function CreatNonceStr($length = 5, $type = 0) {
		$arr = array(1 => "0123456789", 2 => "abcdefghijklmnopqrstuvwxyz", 3 => "ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4 => "~@#$%^&*(){}[]|");
		if ($type == 0) {
			array_pop($arr);
			$string = implode("", $arr);
		} elseif ($type == "-1") {
			$string = implode("", $arr);
		} else {
			$string = $arr[$type];
		}
		$count = strlen($string) - 1;
		$code = '';
		for ($i = 0; $i < $length; $i++) {
			$code .= $string[rand(0, $count)];
		}
		return $code;
	}
	/**
	 * 扫码模式一回复微信业务状态
	 * @param array $resData	业务结果信息数组
	 * @return
	 */
	public function ReturnModRes(array $resData){
		$res=array(
            'return_code'=>$resData['returnCode'],
            "appid"=>$this->appid,
            "sub_appid"=>$this->sub_appid,
            "mch_id"=>$this->mch_id,
            "sub_mch_id"=>$this->sub_mch_id,
            'nonce_str'=>$resData['nonceStr'],
            'prepay_id'=>$resData['prepayId'],
            'result_code'=>$resData['resultCode'],
            'err_code_des'=>$resData['errCodeDes']
        );
		return $this->SortDicXml($res,$this->MD5Upper($this->SortDic($res)));
	}
	private function SortDic(array $strDic){
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
		$sortDic.="key=".$this->secretKey;
		return $sortDic;
	}
	private function MD5Upper($str){
		return strtoupper(md5($str));
	}
	private function SortDicXml(array $strDic,$keySign){
		ksort($strDic);
		$tmp='';
		foreach ($strDic as $key => $value)
			$tmp.="<".$key.">".$value."</".$key.">";
		$sortXml="<xml>".$tmp."<sign>".$keySign."</sign></xml>";
		return $sortXml;
	}
	//扫码模式一字典排序并返回url
	private function SortDicUrl(array $qrDic,$keySign){
		//排序
		ksort($qrDic);
		//排列成键值对
		$sortDic='';
		foreach ($qrDic as $key => $value)
			$sortDic.=$key."=".$value."&";
		return 'weixin://wxpay/bizpayurl?'.$sortDic.'sign='.$keySign;
	}
	//扫码模式一产品ID排序并返回url
	private function SotrProId($proid){
		$qrDic=array(
			"appid"=>$this->appid,
			"mch_id"=>$this->mch_id,
			"time_stamp"=>(string)time(),
			"nonce_str"=>$this->CreatNonceStr(5,0),
			"product_id"=>$proid,
		);
		return $this->SortDicUrl($qrDic,$this->MD5Upper($this->SortDic($qrDic)));
	}
	//获取用户IP地址
	private function GetClientIP() {
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

	public static function createWxPayUnifiedOrder($openID, $orderNo, $money, $product='学费支付') {
		$wxPay = new WxPay();
		$wcMutually = new WeChatMutually();
		$payOrder=array(
			'out_trade_no'=>$orderNo,
			'userIP'=>request()->ip(),
			'trade_type'=>'JSAPI',
			'payAmount'=>$money*100,
			'body'=>$product,
			'detail'=>"弘文教育 - ".$product,
			'openId'=>$openID,
			'sceneInfo'=>''
		);
		//生成预支付订单
		$prexml=$wxPay->GeneratePrepaidOrder($payOrder);
		//提交预支付信息
		$postxml=$wcMutually->PostCurl('https://api.mch.weixin.qq.com/pay/unifiedorder',$prexml);
		//获取预支付结果
		$payer=$wcMutually->XmlToArray($postxml);
		if($wcMutually->XmlStr($payer,"return_code")=='FAIL'){
			$msg=$wcMutually->XmlStr($payer,"return_msg");
			return json(['status'=>false,'msg'=>'调起微信支付失败','data'=>''],201);
		}
		//提取支付参数
		$nonce_str=$wcMutually->XmlStr($payer,"nonce_str");
		$prepay_id=$wcMutually->XmlStr($payer,"prepay_id");
		session('WeChatPayOrderNo',$orderNo);
		//返回前台JS支付数据
		return ['status'=>true,'msg'=>'准备支付','data'=>$wxPay->CreateJSApi($nonce_str,$prepay_id)];
	}
}
