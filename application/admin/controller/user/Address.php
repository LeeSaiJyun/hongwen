<?php

namespace app\admin\controller\user;

use app\common\controller\Backend;

/**
 * 用户地址
 *
 * @icon fa fa-circle-o
 */
class Address extends Backend
{
    
    /**
     * Address模型对象
     * @var \app\admin\model\user\Address
     */
    protected $model = null;
    protected $searchFields = "name,address,telephone";

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\UserAddress;
        $this->view->assign("isDefaultList", $this->model->getIsDefaultList());
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
//        $this->relationSearch = true;
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
                ->with(['province','city','area'])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['province','city','area'])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();


            foreach ($list as $row) {


            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");

            //省市区
            $province = $this->request->post('province');
            $params['province_id'] = $province;

            $city = $this->request->post('city');
            $params['city_id'] = $city;

            $area = $this->request->post('area');
            $params['area_id'] = $area;


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
    

}
