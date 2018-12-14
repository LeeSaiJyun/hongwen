<?php
/**
 * Created by PhpStorm.
 * User: mao
 * Date: 18-12-8
 * Time: 上午10:28
 */

namespace app\api\library\WxPay;


class WeChatMutually {

	/**
	 * post https请求
	 * @param string $url 请求网址
	 * @param string $data 提交数据
	 */
	public function PostCurl($url,$data){
		$ch=curl_init();//初始化
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);//超时时间
		//https请求
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$result=curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	/**
	 * Get https 请求
	 * @param string $url 请求地址
	 */
	public function GetCurl($url){
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		//https请求
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$result=curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	/**
	 * xml反序列化成数组
	 * @param string $xml xml格式字符串
	 */
	public function XmlToArray($xml){
		$reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
		if(preg_match_all($reg, $xml, $matches))
		{
			$count = count($matches[0]);
			$arr = array();
			for($i = 0; $i < $count; $i++)
			{
				$key= $matches[1][$i];
				$val =$this->XmlToArray( $matches[2][$i] );  // 递归
				if(array_key_exists($key, $arr))
				{
					if(is_array($arr[$key]))
					{
						if(!array_key_exists(0,$arr[$key]))
						{
							$arr[$key] = array($arr[$key]);
						}
					}else{
						$arr[$key] = array($arr[$key]);
					}
					$arr[$key][] = $val;
				}else{
					$arr[$key] = $val;
				}
			}

			return $arr;
		}else{
			return $xml;
		}
	}
	/**
	 * 提取指xml节点的值
	 * @param array $array 数组
	 * @param string $strSign 节点名称
	 */
	public function XmlStr($array,$sign){
		foreach($array as $k=>$v){
			if(strpos($v[$sign],'<![CDATA[')!==false){
				$str=substr($v[$sign],9);
				$str=rtrim($str,']]>');
				return $str;
			}
			$str=$v[$sign];
			return $str;
		}
	}
	/**
	 * 数组构造微信的xml
	 * @param array $array 数组
	 */
	public function ArrayToXml($array){
		$xml="<xml>";
		while(list($key,$value)=each($array)){
			$xml.="<".$key.">"."<![CDATA[".$value."]]>"."</".$key.">";
		}
		return $xml.="</xml>";
	}
	/**
	 * 获取微信access_token并写入文件缓存
	 */
	public function GetAcctoken(){
		$url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->secret;
		$json=$this->GetCurl($url);
		file_put_contents("/tmp/api.log",date('Y-m-d H:i:s',time()).' '.$json."\n", FILE_APPEND);
		//反序列化json
		$res=json_decode($json,true);
		if(!array_key_exists('access_token',$res)){
			$this->GetAcctoken();
		}
		cache('AccToken',$res['access_token'],7000);
		return $res['access_token'];
	}
	public function GetAcctok(){
		$accToken=cache('AccToken');
		if(empty($accToken) || $accToken==false){
			$accToken=$this->GetAcctoken();
		}
		return $accToken;
	}
	/**
	 * 获取JcacheDK并写入文件缓存
	 */
	private function GetJsApi(){
		$url="https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$this->GetAcctok()."&type=jsapi";
		$json=$this->GetCurl($url);
		file_put_contents("/tmp/apijs.log",date('Y-m-d H:i:s',time()).' '.$json."\n", FILE_APPEND);
		$res=json_decode($json,true);
		cache('JsApiToken',$res['ticket'],7000);
		return $res['ticket'];
	}
	public function GetJsapiToken(){
		$jsToken=cache('JsApiToken');
		if($jsToken==false){
			$jsToken=$this->GetJsApi();
		}
		return $jsToken;
	}
	public function GetUserMsg($openid){
		$isget=cache('GetWeChatUserMsg_'.$openid);
		if(!empty($isget)){
			return $isget;
		}
		$accToken=$this->GetAcctok();
		$url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accToken."&openid=".$openid."&lang=zh_CN";
		$json=$this->GetCurl($url);
		$res=json_decode($json,true);
		file_put_contents("/tmp/wcusermsg.log",$openid.' '.$json."\n", FILE_APPEND);
		if(empty($res)){
			return null;
		}
		if(array_key_exists('errcode',$res)&&$res['errcode']=='40001'){
			cache('AccToken',null);
			$this->GetUserMsg($openid);
		}
		$province="";
		$city="";
		$data['openid']=$openid;
		$data['unionid']='';
		if(empty($res)){
			return $data;
		}
		while(list($key,$value)=each($res)){
			if($key=="openid"){
				$data['openid']=$value;
			}
			if($key=="nickname"){
				$data['wnick']=$this->remove_emoji($value);
			}
			if($key=="sex"){
				switch($value){
					case "0":
						$data['wsex']="女";
						break;
					case "1":
						$data['wsex']="男";
						break;
					default :
						$data['wsex']="未知";
				}
			}
			if($key=="headimgurl"){
				$data['whead']=$value;
			}
			if($key=="province"){
				$province=$value;
			}
			if($key=="city"){
				$city=$value;
			}
			if($key=="subscribe"){
				$data['subscribe']=$value;
			}
			if($key=="unionid"){
				$data['unionid']=$value;
			}
		}
		if(empty($data['subscribe'])){
			$data['subscribe']=0;
		}
		$data['waddr']=$province.$city;
		if($res['subscribe']>0){
			cache('GetWeChatUserMsg_'.$openid,$data);
		}
		//file_put_contents("/tmp/wxusermsg.log",date('Y-m-d H:i:s',time()).' '.$data['openid'].' '.$data['wnick'].' '.$data['whead']."\n", FILE_APPEND);
		//$data['id']=M('user')->add($data);
		return $data;
	}
	private function coverString($text){
		$text=urlencode($text);
		$text=str_replace("%", "\\x", $text);
		return $text;
	}
	/**
	 * 表情符号转换为一个特殊符号,默认是"#"
	 * @param unknown_type $text
	 * @param unknown_type $replaceStr
	 */
	/*public function emoji_to_string($text,$replaceStr=""){

		$text=str_ireplace(array_keys($this->glob['names']), $this->coverString($replaceStr), $this->coverString($text));
		$text=str_replace("\\x", "%", $text);
		return urldecode($text);
	}*/
	private function remove_emoji($text){
		return preg_replace('/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $text);
	}

}
