<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Db;
use think\Loader;
use think\Request;

/**
 * 预约咨询接口
 * @author leesaijyun
 */
class Reservation extends Api
{

	const API_URL = "/api/reservation";

    /**
     * 模型对象
     * @var \think\Model
     */
    protected $model = null;

    protected $noNeedLogin = [''];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\api\model\Reservation();
    }

    /**
     * @label 预约
     * @param name:姓名
     * @param telephone:手机号
     * @param appointedtime:预约时间
     */
    public function create(Request $request){
        $data['name'] = $request->param('name');
        $data['telephone'] = $request->param('telephone');
        $data['appointedtime'] = $request->param('appointedtime');

        $data['user_id'] = $this->auth->id;

        $validate = validate('Reservation');

        //数据校验
        if(!$validate->check($data)){
            $this->error($validate->getError());
        }
        $result = $this->model->allowField(['user_id','name','telephone','appointedtime'])->save($data);
        $this->success($result);
    }


}
