<?php

namespace app\api\controller\school;

use app\common\controller\Api;
use think\Request;

/**
 * 院校专业关系管理
 */
class Major extends Api{
    const API_URL = "/api/school/major";

    protected $model = null;

    protected $noNeedLogin = ['getList'];
    protected $noNeedRight = ['*'];

    public function _initialize(){
        parent::_initialize();
        $this->model = new \app\admin\model\school\Major();
    }


	public function getList(Request $request) {
		$cat_access_id = $request->get('cat_access_id/d');

		$m_school = new \app\admin\model\school\CatAccess();
		if($cat_access_id){
			$data = $m_school
				->with(['major'])
				->order('id')
				->where('id',$cat_access_id)
				->find();
			if($data){
				$this->success('success',$data['major']);
			}else{
				$this->error('院校数据不存在');
			}
		}else{
			$this->error('院校不存在');
			$data = $this->model
				->field('id,name,title_image,brief')
				->order('id')
				->select();
		}
		$this->success('success',[]);
    }

}
