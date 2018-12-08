<?php

namespace app\api\library\Aliyun;

use app\api\library\Aliyun\Core\Config;
use app\api\library\Aliyun\Core\Profile\DefaultProfile;
use app\api\library\Aliyun\Core\DefaultAcsClient;
use app\api\library\Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;



// 加载区域结点配置
Config::load();

/**
 * Class Sms
 * 这是短信服务API产品的DEMO程序，直接执行此文件即可体验短信服务产品API功能
 * (只需要将AK替换成开通了云通信-短信服务产品功能的AK即可)
 * 备注:Demo工程编码采用UTF-8
 */
class Sms {

	static $acsClient = NULL;

	/**
	 * 取得AcsClient
	 * @return DefaultAcsClient
	 */
	public static function getAcsClient() {
		//产品名称:云通信短信服务API产品,开发者无需替换
		$product = "Dysmsapi";

		//产品域名,开发者无需替换
		$domain = "dysmsapi.aliyuncs.com";

		// TODO 此处需要替换成开发者自己的AK (https://ak-console.aliyun.com/)
		$accessKeyId = "LTAI40pfPSsO8fv6"; // AccessKeyId

		$accessKeySecret = "ZMVqw2pd4x3aCxIySReBPjrKQiAb8O"; // AccessKeySecret

		// 暂时不支持多Region
		$region = "cn-hangzhou";

		// 服务结点
		$endPointName = "cn-hangzhou";


		if (static::$acsClient == NULL) {

			//初始化acsClient,暂不支持region化
			$profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

			// 增加服务结点
			DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

			// 初始化AcsClient用于发起请求
			static::$acsClient = new DefaultAcsClient($profile);
		}
		return static::$acsClient;
	}

	/**
	 * 发送短信
	 * @return stdClass
	 */
	public static function sendSms($mobile, $code) {

		// 初始化SendSmsRequest实例用于设置发送短信的参数
		$request = new SendSmsRequest();

		//可选-启用https协议
		//$request->setProtocol("https");

		// 必填，设置短信接收号码
		$request->setPhoneNumbers($mobile);

		// 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
		$request->setSignName("弘文教育");

		// 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
		$request->setTemplateCode("SMS_151845444");

		// 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
		$request->setTemplateParam(json_encode([  // 短信模板中字段的值
		                                          "code"    => $code,
		                                          "product" => "HonWen",
		], JSON_UNESCAPED_UNICODE));

		// 可选，设置流水号
		//$request->setOutId("yourOutId");

		// 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
		//$request->setSmsUpExtendCode("1234567");

		// 发起访问请求
		$acsResponse = static::getAcsClient()->getAcsResponse($request);

		return $acsResponse;
	}

}
