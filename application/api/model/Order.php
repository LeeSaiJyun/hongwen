<?php

namespace app\api\model;

use think\Model;

class Order extends Model
{
    // 表名
    protected $name = 'order';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';


	/**
	 * 创建订单
	 * @param $user_id  int     用户ID
	 * @param $money    float   金额
	 * @param $paymentdata string  缴费类型:application=报名费  tuition=学费
	 * @return array
	 */
	public function createOrder($user_id, $money, $paymentdata){
        $user_id = intval($user_id);
        $orderno = create_orderno();
        $data = '';
        if($paymentdata === 'tuition'){
            $m_user  = new \app\common\model\User;
            $info = $m_user->getStudentInfo($user_id);
            if($info){
                $data = serialize($info);
            }
        }elseif($paymentdata==='application'){
            $m_application = new Application();
            $info = $m_application->getStudentInfo($user_id);
            if($info){
                $data = serialize($info);
            }
        }
        $add = [
            'user_id' => $user_id,
            'data' => $data,
            'money' => $money,
            'paymentdata' => $paymentdata,
            'orderno'=>$orderno,
            "createtime" => time(),
            "updatetime" => 0,
        ];
        $orderID = $this->insert($add, false, true);
        $add["order_id"] = $orderID;
        return $add;

    }


}
