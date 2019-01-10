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
        $this->model = new \app\admin\model\school\School();
    }

    /**
     * @label 获取院校信息
     * @param cat_id:类别ID(不传就获取全部)
     */
    public function getList(Request $request){
        $type_id = $request->get('type_id/d');

        $m_type = new \app\admin\model\school\Type;
        if($type_id){
			$data =$m_type
				->with(['school'])
				->order('id')
				->where('id',$type_id)
				->find();
			if($data){
				$this->success('success',$data['school']);
			}else{
				$this->error('类型数据不存在');
			}
        }else{
			$this->error('类型不存在');
			$data = $this->model
				->field('id,name,title_image,brief')
				->order('id')
				->select();
        }
        $this->success('success',[]);
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
		$data = $this->model->field('id,name,title_image,brief')->with(['type'])->find($id);
		$this->success('success',$data);
    }


}
