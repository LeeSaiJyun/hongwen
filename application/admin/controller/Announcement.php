<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 公告
 *
 * @icon fa fa-circle-o
 */
class Announcement extends Backend
{
    
    /**
     * Announcement模型对象
     * @var \app\admin\model\Announcement
     */
    protected $model = null;
//    protected $dataLimit = 'auth';          //默认基类中为false，表示不启用，可额外使用auth和personal两个值
//    protected $dataLimitField = 'admin_id'; //数据关联字段,当前控制器对应的模型表中必须存在该字段

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Announcement;
		$this->view->assign("statusList", ['0' => __('Status 0'),'1' => __('Status 1')]);
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $params['admin_id'] = $this->auth->id;      //添加发布公告的admin_id

            if ($params) {
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($this->model->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $params['admin_id'] = $this->auth->id;      //添加发布公告的admin_id


            if ($params) {
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($row->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    public function change($ids = null)
    {
        $ids = intval($ids);
        if ($ids){
            $data  = $this->model->get($ids);
            if($data->status === 0 ){
                $this->model->isUpdate(true)->save(['status'  => 1 ], ['id' => $ids]);;
                $this->success('当前公告已开启');
            }elseif ($data->status === 1){
                $this->model->isUpdate(true)->save(['status'  => 0 ], ['id' => $ids]);;
                $this->success('当前公告已隐藏');
            }else{
                $this->error(__('Status error', ''));
            };
        }else{
            $this->error(__('Parameter %s can not be empty', ''));
        }


    }
}
