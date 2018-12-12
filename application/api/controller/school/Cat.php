<?php

namespace app\api\controller\school;

use app\common\controller\Api;

/**
 * 院校类别信息
 */
class Cat extends Api
{
    protected $model = null;
    const API_URL = "/api/school/cat";

    protected $noNeedLogin = ['getList'];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\api\model\Schoolcat();
    }

    /**
     * @label 院校类别信息
     */
    public function getList(){
        $data = $this->model->order('id')->column('name','id');
        $this->success('success',$data);
    }


}
