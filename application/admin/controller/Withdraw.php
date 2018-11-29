<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 提现
 *
 * @icon fa fa-circle-o
 */
class Withdraw extends Backend
{
    
    /**
     * Withdraw模型对象
     * @var \app\admin\model\Withdraw
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Withdraw;
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with('admin')
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with('admin')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    //审核
    public function audit($ids = null)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row)
            $this->error(__('No Results were found'));
        if ($this->request->isAjax()) {
            $audit_flag = $this->request->post('audit_flag');
            if(!$audit_flag){
                $this->error(__('No audit flag'));
            }elseif($audit_flag == 'accept'){
                //todo  根据审核状态进行对应操作
                $this->success("accept请求成功", null, ['id' => $ids]);
            }elseif($audit_flag == 'reject'){
                //todo  根据审核状态进行对应操作
                $this->success("reject请求成功", null, ['id' => $ids]);
            }
        }
        $this->view->assign("row", $row->toArray());
        return $this->view->fetch();
    }

    public function add( $ids = null){

    }
    public function del( $ids = null)
    {

    }

    public function destroy($ids = null)
    {

    }

    public function edit( $ids = null )
    {

    }

    public function multi($ids = null)
    {

    }

}
