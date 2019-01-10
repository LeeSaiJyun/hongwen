<?php

namespace app\admin\model\school;

use think\Model;

class Major extends Model
{
    // 表名
    protected $name = 'school_major';

	public function schoolCatAccess() {
		return $this->belongsToMany('SchoolCatAccess','SchoolMajorAccess','school_cat_access_id');
	}

}
