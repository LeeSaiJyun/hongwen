<?php

namespace app\api\controller\school;

use app\common\controller\Api;

/**
 * 院校类别信息
 */
class Cat extends Api
{
    protected $model = null;

    protected $noNeedLogin = [''];
    protected $noNeedRight = ['getList'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\api\model\Schoolcat();
    }

    /**
     * 院校类别信息
     * @param $test int
     *
     */
    public function getList(){
        $data = $this->model->order('id')->column('name','id');
        $this->success('success',$data);
    }


}
