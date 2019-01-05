<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Db;
use think\Request;

/**
 * 首页接口
 */
class Index extends Api
{
	private $token = 'hongwenjiaoyu';

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * api
     */
    public function index(){
        $this->success('请求成功');
    }

	//微信接入
	public function  nonce(){
		if($this->checkSignature()){
			echo  Request::instance()->get('echostr');
		}else{
			echo  Request::instance()->get('echostr');
			echo"\nerror";
		}
	}

	/**
	 * 检查签名是否来自微信服务器
	 * @return bool
	 */
	private function checkSignature() {
		$signature = Request::instance()->get('signature');
		$timestamp = Request::instance()->get('timestamp');
		$nonce = Request::instance()->get('nonce');

		$token = $this->token;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);    //字典排序
		$tmpStr = implode($tmpArr);
		$tmpStr = sha1($tmpStr);
		if ($tmpStr == $signature) {
			return true;
		} else {
			return false;
		}
	}

}
