<?php

namespace app\api\controller\school;

use app\common\controller\Api;
use think\Db;
use think\Request;

/**
 * 院校信息
 */
class School extends Api{
    const API_URL = "/api/school/school";

    protected $model = null;

    protected $noNeedLogin = ['getList','getDetail'];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\School();
    }

    /**
     * @label 获取院校信息
     * @param cat_id:类别ID(不传就获取全部)
     */
    public function getList(Request $request){
        $cat_id = $request->get('cat_id/d');
        if($cat_id){
            $where = ['cat_id' => $cat_id];
        }else{
            $where=[];
        }
        $data = $this->model->field('id,cat_id,name,title_image,brief')->order('id')->where($where)->select();
        $this->success('success',$data);
    }

	/**
	 * @label 根据ID获取院校信息
	 * @param cat_id:类别ID(不传就获取全部)
	 */
	public function getDetail() {
		$id = input("id");
		if(!$id){
			$this->error('id不能为空');
		}
		$data = $this->model->field('id,cat_id,name,title_image,brief')->find($id);
		$this->success('success',$data);
    }


}
