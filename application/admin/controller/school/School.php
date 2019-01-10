<?php

namespace app\admin\controller\school;

use app\admin\model\school\SchoolAccess;
use app\common\controller\Backend;
use think\Db;

/**
 * 院校信息
 *
 * @icon fa fa-circle-o
 */
class School extends Backend {

	/**
	 * School模型对象
	 * @var \app\admin\model\school\School
	 */
	protected $model = null;
	protected $searchFields = 'name';

	public function _initialize() {
		parent::_initialize();
		$this->model = new \app\admin\model\school\School;
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
		//设置过滤方法
		$this->request->filter(['strip_tags']);
		if ($this->request->isAjax()) {
			//如果发送的来源是Selectpage，则转发到Selectpage
			if ($this->request->request('keyField')) {
				return $this->selectpage();
			}
			list($where, $sort, $order, $offset, $limit) = $this->buildparams();
			$total = $this->model
				->where($where)
				->order($sort, $order)
				->count();

			$list = $this->model
				->where($where)
				->order($sort, $order)
				->limit($offset, $limit)
				->with(['type','cat'])
				->select();
			$list = collection($list)->toArray();
			foreach ($list as &$row) {
				$type_array = [];
				foreach ($row['type'] as $type_row) {
					$type_array[] = $type_row['type_name'];
				}
				$row['type_text'] = implode(',',$type_array);
			}
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
						$type = explode(',',$this->request->post('school_type'));
						foreach ($type as $value){
							$dataset[] = ['school_id' => $result, 'school_type_id' => $value];
						}
						$m_SchoolAccess =new SchoolAccess();
						$m_SchoolAccess->saveAll($dataset);

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
						$type = explode(',',$this->request->post('school_type'));
						foreach ($type as $value){
							$dataset[] = ['school_id' => $ids, 'school_type_id' => $value];
						}
						$m_SchoolAccess =new SchoolAccess();
						$m_SchoolAccess->where('school_id', $ids)->delete();
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
		$typedata = Db::name('school_access')->where('school_id',$ids)->value('GROUP_CONCAT(school_type_id) as ids');

		$this->view->assign('typedata', $typedata);
		$this->view->assign("row", $row);
		return $this->view->fetch();
	}



}
