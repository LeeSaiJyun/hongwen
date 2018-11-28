<?php

namespace app\admin\controller\school;

use app\common\controller\Backend;
use think\Db;

/**
 * 专业信息
 *
 * @icon fa fa-circle-o
 */
class Major extends Backend
{
    
    /**
     * Major模型对象
     * @var \app\admin\model\Major
     */
    protected $model = null;

    protected $noNeedRight = ['selectbyschool'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Major;
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    public function selectbyschool()
    {
        if ($this->request->isAjax()) {

            $school_id = $this->request->request("school_id/d");
            if(!$school_id){
                if ($this->request->request('keyField')) {
                    return $this->selectpage();
                }
            }else{
                //        halt($school_id);

//        $list = \app\admin\model\Schoolmajor::where($where)->select();
//
//        $total = \app\admin\model\Schoolmajor::where($where)->count();

                $sql = "SELECT count(*) as count FROM `fa_major` where FIND_IN_SET( id, (SELECT major_ids from fa_school_major where id ={$school_id}) );";
                $total = Db::query($sql);
                $sql = "SELECT * FROM `fa_major` where FIND_IN_SET( id, (SELECT major_ids from fa_school_major where id ={$school_id}) );";
                $list = Db::query($sql);

//        SELECT * FROM `fa_major` where FIND_IN_SET( id, (SELECT major_ids from fa_school_major where id =1) );
                return json(['list' => $list, 'total' => $total[0]['count']]);
            }

        }
    }
    

}
