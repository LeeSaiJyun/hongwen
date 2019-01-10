<?php

namespace app\admin\model\school;

use think\Model;
use think\model\Pivot;

class CatAccess extends Pivot
{
    // 表名
    protected $name = 'school_cat_access';

    public function school()
    {
        return $this->belongsTo('School', 'school_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function cat()
    {
        return $this->belongsTo('Cat', 'school_cat_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

	public function major()
	{
		return $this->belongsToMany('Major', 'SchoolMajorAccess', 'school_major_id');
	}

	/*public function schoolCatAccess() {
		return $this->belongsToMany('SchoolCatAccess','SchoolMajorAccess','school_cat_access_id');
	}*/

}
