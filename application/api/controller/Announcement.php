<?php

namespace app\api\controller;

use app\common\controller\Api;

/**
 * 公告
 */
class Announcement extends Api {
	const API_URL="/api/Announcement";

	protected $model;

	protected $noNeedLogin=['getList'];
	protected $noNeedRight='*';


	/**
	 * @label 获取公告
	 */
	public function getList() {
		$data=\app\admin\model\Announcement::field('id,title,announcement_content,createtime')->where(['status'=>1])->select();

		foreach ($data as $row=>&$value) {
			if ($value['createtime']) $value['createtime']=date('Y-m-d H:i:s', $value['createtime']);
		}
		$this->success('success', $data);
	}
	protected function success($msg = '', $data = null, $code = 200, $type = null, array $header = [])
	{
		$this->result($msg, $data, $code, $type, $header);
	}

}
