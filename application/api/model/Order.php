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
     *
     * @param $user_id  int     用户ID
     * @param $money    float   金额
     * @param $paymentdata string  缴费类型:application=报名费  tuition=学费
     */
    public function createOrder($user_id,$money,$paymentdata){
        $user_id = intval($user_id);
        $orderno = create_orderno();
        $this->allowField(['user_id','money','paymentdata','orderno'])->save(['user_id' => $user_id, 'money' => $money, 'paymentdata' => $paymentdata,'orderno'=>$orderno]);
    }


}
