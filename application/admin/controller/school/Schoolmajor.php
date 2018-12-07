<?php

namespace app\admin\controller\school;

use app\common\controller\Backend;

/**
 * 院校专业关系管理
 *
 * @icon fa fa-circle-o
 */
class Schoolmajor extends Backend
{
    
    /**
     * Schoolmajor模型对象
     * @var \app\admin\model\Schoolmajor
     */
    protected $model = null;
    protected $searchFields = "school.name";

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Schoolmajor;
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->with(['major','school'])
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['major','school'])
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();


            //查询所有major
            $majorList = \app\admin\model\Major::withTrashed()->field('id,name')->select();
            $majorList = collection($majorList)->toArray();
            //转换
            $majorList = array_column($majorList, 'name', 'id');

            $list = collection($list)->toArray();

            foreach ($list as $k => &$v)
            {
                //major['id']转换成major['name']
                $major_ids = explode(',',$v['major_ids']);
                $majorNameList = [];
                foreach ($major_ids as $major_id ){
                    array_push($majorNameList, $majorList[$major_id]);
                }
                $v['major_text'] = implode(',', $majorNameList);
            }
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
}
