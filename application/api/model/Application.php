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
        $ret = $this->field("s.name as school,m.name as major,null as grade")
                ->alias('app')
                ->join('fa_school s','app.school_id = s.id','LEFT')
                ->join('fa_major m','app.major_id = m.id','LEFT')
                ->order('app.id desc')
                ->where(['user_id'=>$user_id,'status'=>0,'pay_status'=>0])
                ->find();
        if($ret) {
            $ret = $ret->toArray();
            return $ret;
        }
        return $ret;

    }
}
