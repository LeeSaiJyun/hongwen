<?php

namespace app\admin\controller\school;

use app\common\controller\Backend;

/**
 * 院校类别关系管理
 *
 * @icon fa fa-circle-o
 */
class Cataccess extends Backend {

	/**
	 * CatAccess模型对象
	 * @var \app\admin\model\school\CatAccess
	 */
	protected $model = null;

	public function _initialize() {
		parent::_initialize();
		$this->model = new \app\admin\model\school\CatAccess;

	}

	/**
	 * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
	 * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
	 * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
	 */


	/**
	 * 查看
	 */
	public function index() {
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
				->with(['school', 'cat'])
				->where($where)
				->order($sort, $order)
				->count();

			$list = $this->model
				->with(['school', 'cat'])
				->where($where)
				->order($sort, $order)
				->limit($offset, $limit)
				->select();

			foreach ($list as &$row) {
				$row->getRelation('school')->visible(['name']);
				$row->getRelation('cat')->visible(['name']);
				$row['name'] = $row['school']['name'].'-'.$row['cat']['name'];
			}
			$list = collection($list)->toArray();

			$result = array("total" => $total, "rows" => $list);

			return json($result);
		}
		return $this->view->fetch();
	}

	protected function selectpage()
	{
		//设置过滤方法
		$this->request->filter(['strip_tags', 'htmlspecialchars']);

		//搜索关键词,客户端输入以空格分开,这里接收为数组
		$word = (array)$this->request->request("q_word/a");
		//当前页
		$page = $this->request->request("pageNumber");
		//分页大小
		$pagesize = $this->request->request("pageSize");
		//搜索条件
		$andor = $this->request->request("andOr", "and", "strtoupper");
		//排序方式
		$orderby = (array)$this->request->request("orderBy/a");
		//显示的字段
		$field = $this->request->request("showField");
		//主键
		$primarykey = $this->request->request("keyField");
		//主键值
		$primaryvalue = $this->request->request("keyValue");
		//搜索字段
		$searchfield = (array)$this->request->request("searchField/a");
		$order = [];
		foreach ($orderby as $k => $v) {
			$order[$v[0]] = $v[1];
		}
		$field =  "CONCAT(s.name,'-',c.name)";
		//如果有primaryvalue,说明当前是初始化传值
		if ($primaryvalue !== null) {
			$where = ['c_acc.'.$primarykey => ['in', $primaryvalue]];
		} else {
			$where = function ($query) use ($word, $andor, $field, $searchfield) {
				$logic = $andor == 'AND' ? '&' : '|';
				$searchfield = is_array($searchfield) ? implode($logic, $searchfield) : $searchfield;
				foreach ($word as $k => $v) {
					$query->where("$field like '%{$v}%'");
				}
			};
		}

		$list = [];
		$total = $this->model->alias('c_acc')
			->field("$field as name")
			->join('fa_school s','c_acc.school_id = s.id','LEFT')
			->join(' fa_school_cat c','c_acc.school_cat_id = c.id','LEFT')
			->where($where)
			->count();

		if ($total > 0) {
			$datalist = $this->model->alias('c_acc')
				->field("c_acc.id,$field as name")
				->join('fa_school s','c_acc.school_id = s.id','LEFT')
				->join(' fa_school_cat c','c_acc.school_cat_id = c.id','LEFT')
				->where($where)
				->order($order)
				->page($page, $pagesize)
				->select();

			foreach ($datalist as $index => $item) {
				unset($item['password'], $item['salt']);
				$list[] = [
					$primarykey => isset($item[$primarykey]) ? $item[$primarykey] : '',
					'name'      => isset($item['name']) ? $item['name'] : ''
				];
			}
		}

		//这里一定要返回有list这个字段,total是可选的,如果total<=list的数量,则会隐藏分页按钮
		return json(['list' => $list, 'total' => $total]);
	}
}
