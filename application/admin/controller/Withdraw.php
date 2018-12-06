<?php

namespace app\admin\controller;

use app\admin\model\User;
use app\common\controller\Backend;
use think\Db;
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
//        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $total = $this->model
                ->with(['admin','user','userbank'])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['admin','user','userbank'])
                //'admin'=> function ($query) {$query->field('username,nickname');},
                //'user' => function ($query) {$query->field('username,nickname');},
                //'bank' => function ($query) {$query->field('name,bankname,banklocation,bankcard');}
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
        $row = $this->model->get(['id' => $ids],['user','userbank']);
        if ($this->request->isAjax()) {
            if(intval($row->status)!== 0 && intval($row->status)!== 1){
                $this->error("该数据已被处理，请刷新重试");
            }
            $audit_flag = $this->request->post('audit_flag');   //审核状态
            //todo  根据审核状态进行对应操作
            switch ( $audit_flag ){
                case 'audit':   //已审核
                    $row->status = 1;
                    $row->save();
                    $this->success("已审核 成功");
                    break;
                case 'accept':  //同意
                    //账号注册时需要开启事务,避免出现垃圾数据
                    Db::startTrans();   // 启动事务
                    try{
                        $m_user = new User();
                        $ret = $m_user->frozenChange($row->user_id,$row->money,false);
                        if($ret) {
                            $row->status = 2;
                            $row->save();
                            Db::commit();     //事务提交
                            $this->success(__("已打款 成功"));
                        }
                        Db::rollback();   //事务回滚
                        $this->error("冻结金额错误");

                    } catch (Exception $e) {
                        Db::rollback();   //事务回滚
                        $this->error($e->getMessage());
                    }
                    break;
                case 'reject':
                    //拒绝
                    Db::startTrans();   // 启动事务
                    try{
                        $m_user = new User();
                        $ret1 = $m_user->frozenChange($row->user_id,$row->money,false);
                        $ret2 = $m_user->balanceChange($row->user_id,$row->money,true);
                        if($ret1 && $ret2){
                            $row->status = -1;
                            $row->save();
                            Db::commit();     //事务提交
                            return $this->success("拒绝 成功");
                        }else{
                            Db::rollback();   //事务回滚
                            $this->error("冻结金额错误");
                        }
                    } catch (Exception $e) {
                        Db::rollback();   //事务回滚
                        $this->error($e->getMessage());
                    }
                    break;
                default:
                    return $this->error(__('No flag!'));
            }
        }
        $this->view->assign("row", $row->toArray());
        return $this->view->fetch();
    }

    public function add($ids = null){ }

    public function del($ids = null){ }

    public function destroy($ids = null){ }

    public function edit($ids = null){ }

    public function multi($ids = null){ }

}
