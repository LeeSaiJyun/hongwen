<?php

namespace app\admin\model;

use think\Model;

class Application extends Model {
	// 表名
	protected $name = 'application';

	// 自动写入时间戳字段
	protected $autoWriteTimestamp = 'int';

	// 定义时间戳字段名
	protected $createTime = 'createtime';
	protected $updateTime = 'updatetime';

	// 追加属性
	protected $append = ['applicationdata_text', 'sex_text', 'status_text', 'applicationtime_text'];

	public function getApplicationdataList() {
		return ['0' => __('Applicationdata 0'), '1' => __('Applicationdata 1')];
	}

	public function getSexList() {
		return ['0' => __('Sex 0'), '1' => __('Sex 1'), '2' => __('Sex 2')];
	}

	public function getStatusList() {
		return ['0' => __('Status 0'), '1' => __('Status 1'), '-1' => __('Status -1')];
	}


	public function getApplicationdataTextAttr($value, $data) {
		$value = $value ? $value : (isset($data['applicationdata']) ? $data['applicationdata'] : '');
		$list = $this->getApplicationdataList();
		return isset($list[$value]) ? $list[$value] : '';
	}


	public function getSexTextAttr($value, $data) {
		$value = $value ? $value : (isset($data['sex']) ? $data['sex'] : '');
		$list = $this->getSexList();
		return isset($list[$value]) ? $list[$value] : '';
	}


	public function getStatusTextAttr($value, $data) {
		$value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
		$list = $this->getStatusList();
		return isset($list[$value]) ? $list[$value] : '';
	}


	public function getApplicationtimeTextAttr($value, $data) {
		$value = $value ? $value : (isset($data['applicationtime']) ? $data['applicationtime'] : '');
		return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
	}

	protected function setApplicationtimeAttr($value) {
		return $value && !is_numeric($value) ? strtotime($value) : $value;
	}


	public function user() {
		return $this->hasOne('User', 'id', 'user_id', [], 'LEFT')->field('id,nickname');
	}

	public function type() {
		return $this->hasOne('\app\admin\model\school\Type', 'id', 'type_id', [], 'LEFT')->field('id,type_name');
	}

	public function school() {
		return $this->hasOne('\app\admin\model\school\School', 'id', 'school_id', [], 'LEFT')->field('id,name');
	}

	public function cat() {
		return $this->hasOne('\app\admin\model\school\Cat', 'id', 'cat_access_id', [], 'LEFT')->field('id,name');
	}

	public function cat_access() {
		return $this->hasOne('\app\admin\model\school\CatAccess', 'id', 'cat_access_id', [], 'LEFT')->field('id,name');
	}

	public function major() {
		return $this->hasOne('\app\admin\model\school\Major', 'id', 'major_id', [], 'LEFT')->field('id,name');
	}

}
