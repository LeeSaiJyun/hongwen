<?php

namespace app\admin\model\school;

use think\Model;

class School extends Model {
	// 表名
	protected $name = 'school';

	// 自动写入时间戳字段
	protected $autoWriteTimestamp = false;

	public function type() {
		return $this->belongsToMany('type','\app\admin\model\school\SchoolAccess','school_type_id');
	}

	public function cat() {
		return $this->belongsToMany('cat','\app\admin\model\school\SchoolCatAccess','school_cat_id');
	}


}
