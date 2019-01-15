<?php

namespace app\api\model;

use think\Model;

class Application extends Model
{
    // 表名
    protected $name = 'application';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = 'updatetime';


    /**
     * 根据用户ID获取用户报名院校年级信息
     * @param int $user_id
     * @return array/null {"grade":"2014级","school":"东莞理工","major":"土木工程"}
     * @throws
     */
    public function getStudentInfo($user_id){
        $ret = $this->field('t.type_name as type,s.name as school,c.name as cat,m.name as major')
			->alias('u')
			->join('fa_school_type t','u.type_id = t.id','LEFT')		//类型
			->join('fa_school s','u.school_id = s.id','LEFT')		//学校
			->join('fa_school_cat_access c_acc','u.cat_access_id = c_acc.id','LEFT')  //cat_access
			->join('fa_school_cat c','c_acc.school_cat_id = c.id','LEFT')	//层次
			->join('fa_school_major m','u.major_id = m.id','LEFT')			//专业
			->order('u.id desc')
			->where(['user_id'=>$user_id,'status'=>0,'pay_status'=>0])
			->find();
        if($ret) {
            $ret = $ret->toArray();
            return $ret;
        }
        return $ret;
    }
}
