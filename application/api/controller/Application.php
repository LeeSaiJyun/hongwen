<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Request;

/**
 * 报名申请
 * @author leesaijyun
 */
class Application extends Api
{

	const API_URL = "/api/application";

    protected $model = null;

    protected $noNeedLogin = [''];
    protected $noNeedRight = '*';


    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\api\model\Application();
    }

    /**
     * @label 报名申请
     * @param name:姓名
     * @param telephone:手机号
     * @param appointedtime:预约时间
     */
    public function create(Request $request){
        $apply_data = $request->param();

        //todo    添加用户ID
        $apply_data['user_id'] = $this->auth->id?:1;
        $apply_data['pay_status'] = 0;
        $apply_data['money'] = \think\Config::get("site.registration_fee");     //报名费用


        $validate = validate('Application');
        if(!$validate->check($apply_data)){
            $this->error($validate->getError());
        }
        //查询是否存在当前学校专业的报名
        $field = ['id','user_id', 'name', 'telephone',  'birthday', 'sex', 'ethnic', 'graduation', 'certificate', 'school_id', 'major_id', 'graduationdate', 'graduationmajor'];
        $result = $this->model->field($field)->order('id desc')->where([
            'school_id' => $apply_data['school_id'],
            'major_id' => $apply_data['major_id'],
            'user_id' => $apply_data['user_id'],
        ])->find();
        if($result){
            $this->success('报名已存在',$result,101);
        }

        array_push($field,'idcard', 'updatetime','pay_status','money');
        $this->model->allowField($field)->save($apply_data);
        $this->success('报名成功',['application_id'=>$this->model->id]);
    }


}
