<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class User extends Model
{

    // 表名
    protected $name = 'user';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

	use SoftDelete;
	protected $deleteTime = 'delete_time';
    /*// 追加属性
    protected $append = [
        'prevtime_text',
        'logintime_text',
        'jointime_text'
    ];*/

    protected static function init()
    {
        self::beforeUpdate(function ($row) {
            $changed = $row->getChangedData();
            //如果有修改密码
            if (isset($changed['password'])) {
                if ($changed['password']) {
                    $salt = \fast\Random::alnum();
                    $row->password = \app\common\library\Auth::instance()->getEncryptPassword($changed['password'], $salt);
                    $row->salt = $salt;
                } else {
                    unset($row->password);
                }
            }
        });
    }

    public function getGenderList()
    {
        return ['1' => __('Male'), '0' => __('Female')];
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }



    protected function setPrevtimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setLogintimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setJointimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    public function group()
    {
        return $this->belongsTo('UserGroup', 'group_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    /**
     * 冻结资金修改
     * @param int $id 金额
     * @param int $money 金额
     * @param bool $flag true:增加(用户提现) false:减少(打款)
     * @author lee
     */
    public function frozenChange($id,$money,$inc = true)
    {
        $id = intval($id);
        $money = intval($money);
        if ($inc){
            return $this->where(['id'=>$id,'frozen'=>['>=',$money]])->setInc('frozen', $money);;
        }else{
            return $this->where(['id'=>$id,'frozen'=>['>=',$money]])->setDec('frozen', $money);

        }
    }

    /**
     * 用户余额修改
     * @param int $id 金额
     * @param int $money 金额
     * @param bool $inc true:增加 false:减少
     * @author lee
     */
    public function balanceChange($id,$money,$inc = true)
    {
        $id = intval($id);
        $money = intval($money);
        if ($inc){
            return $this->where(['id'=>$id,'balance'=>['>=',$money]])->setInc('balance', $money);;
        }else{
            return $this->where(['id'=>$id,'balance'=>['>=',$money]])->setDec('balance', $money);
        }
    }

}
