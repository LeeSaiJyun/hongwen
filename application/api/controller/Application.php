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
     * @param idcard:身份证号
     * @param birthday:生日 y-m-d
     * @param sex:性别 0=男,1=女,2=未知
     * @param ethnic:民族
     * @param graduation:最高学历学校
     * @param certificate:证书编号
     * @param graduationdate:毕业时间 y-m-d
     * @param graduationmajor:毕业专业
     * @param school_id:学校
     * @param major_id:专业
     */
    public function create(Request $request){
        $apply_data = $request->param();

        //todo    添加用户ID
        $apply_data['user_id'] = $this->auth->id?:1;
        $apply_data['pay_status'] = 1;//1:未支付
        $apply_data['money'] = \think\Config::get("site.registration_fee");     //报名费用


        $validate = validate('Application');
        if(!$validate->check($apply_data)){
            $this->error($validate->getError());
        }
        //查询是否存在当前学校专业的报名
        $field = ['id','user_id', 'name', 'telephone',  'birthday', 'sex', 'ethnic', 'graduation', 'certificate', 'school_id', 'major_id', 'graduationdate', 'graduationmajor','money'];
        $result = $this->model->field($field)->order('id desc')->where([
            'school_id' => $apply_data['school_id'],
            'major_id' => $apply_data['major_id'],
            'user_id' => $apply_data['user_id'],
        ])->find();
        if($result){
            $this->error('报名已存在',$result);
        }

        array_push($field,'idcard', 'updatetime','pay_status');
        $this->model->allowField($field)->save($apply_data);
        $this->success('报名成功',['application_id'=>$this->model->id]);
    }


}
