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

    /**
     * 模型对象
     * @var \think\Model
     */
    protected $model = null;

    protected $noNeedLogin = [''];
    protected $noNeedRight = ['create'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\api\model\Reservation();
    }

    /**
     * 预约
     * @ApiMethod    API接口请求方法: POST
     * @param $name string 姓名
     * @param $telephone string 手机号
     * @param $appointedtime int 预约时间
     */
    public function create(Request $request)
    {
        $data['name'] = $request->param('name');
        $data['telephone'] = $request->param('telephone');
        $data['appointedtime'] = $request->param('appointedtime');

        //todo    添加用户ID
        $data['user_id'] = $this->auth->id;

        $validate = validate('Reservation');

        //数据校验
        if(!$validate->check($data)){
            $this->error($validate->getError());
        }
        $result = $this->model->allowField(['name','telephone','appointedtime'])->save($data);
        $this->success($result);
    }


}
