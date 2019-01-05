<?php

namespace app\api\controller;

use app\common\controller\Api;

/**
 * 技术培训信息
 */
class Technically extends Api
{
    const API_URL = "/api/Technically";

    protected $model;

    protected $noNeedLogin = ['getList','getDetail'];
    protected $noNeedRight = '*';

    public function _initialize(){
        parent::_initialize();
        $this->model = new \app\admin\model\Technically();
    }

	public function getList() {
		$data = $this->model->field('id,title,title_image')->order('id')->select();
		$this->success('success',$data);
    }

	/**
	 * 获取详情
	 */
	public function getDetail() {
		$id = input("id");
		if(!$id){
			$this->error('id不能为空');
		}
		$data = $this->model->field('id,title,title_image,content')->find($id);
		$this->success('success',$data);
	}

}
