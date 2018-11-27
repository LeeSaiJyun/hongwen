<?php

namespace app\admin\controller;

use app\admin\model\Order;
use app\admin\model\User;
use app\common\controller\Backend;
use think\Config;
use think\Db;

/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{

    /**
     * 查看
     */
    public function index()
    {
        //数据图显示{$num}天前的数据
        $num = 7;
        $seventtime = \fast\Date::unixtime('day', 1 + $num * -1);
        $sql = "select count(id) as total, FROM_UNIXTIME(createtime, '%Y-%m-%d') as time
                    from fa_order where createtime>= '" . $seventtime . "' and createtime < '" . time() . "' group by time;  ";
        $dataList = Db::query($sql);
        $sendlist = array_reduce($dataList, function($v,$w) use ($dataList) {
            $v[$w["time"]]=$w["total"];
            return $v;
        });


        $sql = "select count(id) as total, FROM_UNIXTIME(createtime, '%Y-%m-%d') as time
                    from fa_order where createtime>= '" . $seventtime . "' and createtime < '" . time() . "' group by time;";
        $dataList = Db::query($sql);
        $mobilelist = array_reduce($dataList, function($v,$w) use ($dataList) {
            $v[$w["time"]]=$w["total"];
            return $v;
        });
        /*$sql = "select date(createtime) as time,count(id) as total from (
                    SELECT id,createtime FROM fa_push_log
                    WHERE TO_DAYS(NOW()) - TO_DAYS(createtime) <= $num
                )as test group by date(createtime);";
        $dataList = Db::query($sql);
        $sendlist = array_reduce($dataList, create_function('$v,$w', '$v[$w["time"]]=$w["total"];return $v;'));

        $sql = "select date(time) as time,count(id) as total from (
                    SELECT id,time FROM fa_mobile
                    WHERE TO_DAYS(NOW()) - TO_DAYS(time) <= $num
                )as test group by date(time);";
        $dataList = Db::query($sql);
        $mobilelist = array_reduce($dataList, create_function('$v,$w', '$v[$w["time"]]=$w["total"];return $v;'));*/
        $sendList = $mobileList = [];
        for ($i = 0; $i < $num; $i++) {
            $day = date("Y-m-d", $seventtime + ($i * 86400));
            $sendList[$day] = array_key_exists($day, $sendlist) ? $sendlist[$day] : 0;
            $mobileList[$day] = array_key_exists($day, $mobilelist) ? $mobilelist[$day] : 0;
//            $createlist[$day] = Db::table('shebeimac')->where('time','between time',[$day,$nextday])->count();
//            $paylist[$day] = Db::table('errorlog')->where('time','between time',[$day,$nextday])->fetchSql()->count();
        }




        $hooks = config('addons.hooks');
        $uploadmode = isset($hooks['upload_config_init']) && $hooks['upload_config_init'] ? implode(',', $hooks['upload_config_init']) : 'local';
        $addonComposerCfg = ROOT_PATH . '/vendor/karsonzhang/fastadmin-addons/composer.json';
        Config::parse($addonComposerCfg, "json", "composer");
        $config = Config::get("composer");
        $addonVersion = isset($config['version']) ? $config['version'] : __('Unknown');

        $today_timestamp = strtotime('today');
        $user_model = new User();
        $order_model = new Order();

        $this->view->assign([
            'totaluser'        => $user_model->where('status','normal')->count(),
            'totalviews'       => 999999,
            'totalorder'       => $order_model->where('paymentdata','normal')->count(),
            'totalorderamount' => $order_model->where('paymentdata','normal')->sum('money'),

            'todayuserlogin'   => $user_model->where(['status'=>'normal','logintime'=>['>=',$today_timestamp]])->count(),
            'todayusersignup'  => $user_model->where(['status'=>'normal','jointime'=>['>=',$today_timestamp]])->count(),
            'todayorder'       => $order_model->where(['paymentdata'=>'normal','paytime'=>['>=',$today_timestamp]])->count(),
            'unsettleorder'    => 999999,
            'sevendnu'         => '80%',
            'sevendau'         => '32%',

            'paylist'          => $sendList,
            'createlist'       => $mobileList,
            'addonversion'     => $addonVersion,
            'uploadmode'       => $uploadmode
        ]);

        return $this->view->fetch();
    }


}
