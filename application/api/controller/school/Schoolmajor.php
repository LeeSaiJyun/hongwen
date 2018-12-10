<?php

namespace app\api\controller\school;

use app\common\controller\Api;
use think\Request;

/**
 * 院校专业关系管理
 */
class Schoolmajor extends Api
{
    protected $model = null;

    protected $noNeedLogin = ['getList'];
    protected $noNeedRight = ['*'];

    public function _initialize(){
        parent::_initialize();
        $this->model = new \app\api\model\Schoolmajor();
    }

    /**
     * 获取指定院校的专业信息
     * @param $cat_id int
     */
    public function getList(Request $request){
        $school_id = $request->get('school_id/d');
        if($school_id){
            $where = ['school_id' => $school_id];
        }else{
            $where=[];
        }
        $data = $this->model->where($where)->field(['id,school_id,major_ids'])->select();

        //查询所有major
        $majorList = \app\api\model\Major::field('id,name')->select();
        $majorList = collection($majorList)->toArray();
        //转换
        $majorList = array_column($majorList, 'name', 'id');

        $data = collection($data)->toArray();

        foreach ($data as $k => &$v)
        {
            //major['id']转换成major['name']
            $major_ids = explode(',',$v['major_ids']);
//            $majorNameList = [];
            $v['major'] = [];
            foreach ($major_ids as $major_id ){
                if(array_key_exists($major_id,$majorList)){
//                    array_push($majorNameList, $majorList[$major_id]);
                    $v['major'][$major_id]=$majorList[$major_id];
                }
//                $v['major_text'] = implode(',', $majorNameList);
            }
        }

        $this->success('success',$data);
    }

}
