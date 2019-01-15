<?php

namespace app\common\model;

use think\Model;

/**
 * 会员模型
 */
class User Extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
        'url',
    ];

    /**
     * 获取个人URL
     * @param   string  $value
     * @param   array   $data
     * @return string
     */
    public function getUrlAttr($value, $data)
    {
        if( array_key_exists('id',$data) ){
            return "/u/" . $data['id'];
        }
    }

    /**
     * 获取头像
     * @param   string    $value
     * @param   array     $data
     * @return string
     */
    public function getAvatarAttr($value, $data)
    {
        return $value ? $value : '/assets/img/avatar.png';
    }

    /**
     * 获取会员的组别
     */
    public function getGroupAttr($value, $data)
    {
        return UserGroup::get($data['group_id']);
    }

    /**
     * 获取验证字段数组值
     * @param   string    $value
     * @param   array     $data
     * @return  object
     */
    public function getVerificationAttr($value, $data)
    {
        $value = array_filter((array) json_decode($value, TRUE));
        $value = array_merge(['email' => 0, 'mobile' => 0], $value);
        return (object) $value;
    }

    /**
     * 设置验证字段
     * @param mixed $value
     * @return string
     */
    public function setVerificationAttr($value)
    {
        $value = is_object($value) || is_array($value) ? json_encode($value) : $value;
        return $value;
    }

    /**
     * 根据用户ID获取用户院校年级信息
     * @param int $user_id
     * @return array/null {"grade":"2014级","school":"东莞理工","major":"土木工程"}
     * @throws
     */
    public function getStudentInfo($user_id){
        $ret = $this->alias('u')
			->field('t.type_name as type,s.name as school,c.name as cat,m.name as major')
			->join('fa_school_type t','u.type_id = t.id','LEFT')		//类型
			->join('fa_school s','u.school_id = s.id','LEFT')		//学校
			->join('fa_school_cat_access c_acc','u.cat_access_id = c_acc.id','LEFT')  //cat_access
			->join('fa_school_cat c','c_acc.school_cat_id = c.id','LEFT')	//层次
			->join('fa_school_major m','u.major_id = m.id','LEFT')			//专业
			->order('u.id desc')
            ->find(['u.status' => 'normal','u.id'=>$user_id]);
		if($ret) {
            $ret = $ret->toArray();
            unset($ret['url']);
            return $ret;
        }
        return $ret;
    }

    /**
     * 余额转移到冻结
     * @param $user_id 用户ID
     * @param $money 需要冻结的金额
     * @return mixed
     */
    public static function balanceToFrozen($user_id,$money){
        return self::where('id',$user_id)
            ->dec('balance',$money)
            ->inc('frozen',$money)
            ->update();
    }

    /**
     * 解冻金额
     * @param $user_id 用户ID
     * @param $money 解冻金额
     */
    public static function frozenToBalance($user_id,$money){
        return self::where('id',$user_id)
            ->inc('balance',$money)
            ->dec('frozen',$money)
            ->update();
    }


}
