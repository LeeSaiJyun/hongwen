<?php

namespace app\admin\controller\school;

use app\admin\model\school\SchoolMajorAccess;
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
     * @var \app\admin\model\school\Major
     */
    protected $model = null;
	protected $searchFields = 'm.name';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\school\Major;

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
		/*Db::listen(function($sql,$time,$explain){
			// 记录SQL
			echo $sql. ' ['.$time.'s]';
			// 查看性能分析结果
			dump($explain);
		});*/
		//设置过滤方法
		$this->request->filter(['strip_tags']);
		if ($this->request->isAjax()) {
			//如果发送的来源是Selectpage，则转发到Selectpage
			if ($this->request->request('keyField')) {
				return $this->selectpage();
			}
			list($where, $sort, $order, $offset, $limit) = $this->buildparams();
			$total = $this->model
				->alias('m')
				->field("m.id,m.name")
				->where($where)
				->order($sort, $order)
				->limit($offset, $limit)
				->count();

			$list = $this->model
				->alias('m')
				->field("m.id,m.name,GROUP_CONCAT(CONCAT(s.name,'-',c.name)) as school_cat_access_name")
				->where($where)
				->join('fa_school_major_access m_acc','m.id=m_acc.school_major_id','left')
				->join('fa_school_cat_access c_acc','c_acc.id=m_acc.school_cat_access_id','left')
				->join('fa_school_cat c','c_acc.school_cat_id=c.id','left')
				->join('fa_school s','c_acc.school_id=s.id','left')
				->group('m.id')
				->order($sort, $order)
				->limit($offset, $limit)
				->select();

			$list = collection($list)->toArray();
			$result = array("total" => $total, "rows" => $list);
			return json($result);
		}
		return $this->view->fetch();
	}

	/**
	 * 添加
	 */
	public function add()
	{
		if ($this->request->isPost()) {
			$params = $this->request->post("row/a");
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
					$result = $this->model->allowField(true)->insertGetId($params);

					if ($result !== false) {
						//添加中间表数据
						$type = explode(',',$this->request->post('school_cat'));
						foreach ($type as $value){
							$dataset[] = ['school_major_id' => $result, 'school_cat_access_id' => $value];
						}
						$m_SchoolMajorAccess =new SchoolMajorAccess();
						$m_SchoolMajorAccess->saveAll($dataset);

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
		$m_SchoolAccess =new SchoolMajorAccess();
		if ($this->request->isPost()) {
			$params = $this->request->post("row/a");
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
						//添加中间表数据
						$type = explode(',',$this->request->post('school_cat'));
						foreach ($type as $value){
							$dataset[] = ['school_major_id' => $ids, 'school_cat_access_id' => $value];
						}
						$m_SchoolAccess->where('school_major_id', $ids)->delete();
						$m_SchoolAccess->saveAll($dataset);

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
		$typedata = $m_SchoolAccess::where('school_major_id',$ids)->value('GROUP_CONCAT(school_cat_access_id) as ids');
		$this->view->assign('Majordata', $typedata);
		$this->view->assign("row", $row);
		return $this->view->fetch();
	}

	/**
	 * 删除
	 */
	public function del($ids = "")
	{
		if ($ids) {
			$pk = $this->model->getPk();
			$adminIds = $this->getDataLimitAdminIds();
			if (is_array($adminIds)) {
				$count = $this->model->where($this->dataLimitField, 'in', $adminIds);
			}
			$list = $this->model->where($pk, 'in', $ids)->select();
			//删除中间表
			$m_SchoolMajorAccess =new SchoolMajorAccess();
			$m_SchoolMajorAccess->where('school_major_id', 'in', $ids)->delete();
			$count = 0;
			foreach ($list as $k => $v) {
				$count += $v->delete();
			}
			if ($count) {
				$this->success();
			} else {
				$this->error(__('No rows were deleted'));
			}
		}
		$this->error(__('Parameter %s can not be empty', 'ids'));
	}
}
