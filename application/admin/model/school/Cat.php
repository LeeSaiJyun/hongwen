<?php

namespace app\admin\model\school;

use think\Model;

class Cat extends Model
{
    // 表名
    protected $name = 'school_cat';

	public function school() {
		return $this->belongsToMany('school','\app\admin\model\school\SchoolCatAccess','school_id');
	}

	public function major() {
		return $this->belongsToMany('major','\app\admin\model\school\SchoolMajorAccess','school_major_id');
	}

}
