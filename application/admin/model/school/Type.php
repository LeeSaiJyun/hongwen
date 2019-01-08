<?php

namespace app\admin\model\school;

use think\Model;

class Type extends Model
{
    // 表名
    protected $name = 'school_type';

	public function school() {
		return $this->belongsToMany('school','\app\admin\model\school\SchoolAccess','school_id');
	}
}
