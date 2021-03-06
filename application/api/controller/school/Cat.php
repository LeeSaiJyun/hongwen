<?php

namespace app\api\controller\school;

use app\common\controller\Api;
use think\Request;

/**
 * 院校类别信息
 */
class Cat extends Api
{
    protected $model = null;
    const API_URL = "/api/school/cat";

    protected $noNeedLogin = ['getList'];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\school\Cat();
    }


    public function getList(Request $request){
		$school_id = $request->get('school_id/d');

		$m_school = new \app\admin\model\school\School();
		if($school_id){
			$data = $m_school
				->with(['cat'])
				->order('id')
				->where('id',$school_id)
				->find();
			if($data){
				$this->success('success',$data['cat']);
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
