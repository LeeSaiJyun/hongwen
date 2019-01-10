<?php

namespace app\api\controller\school;

use app\common\controller\Api;

/**
 * 院校类别信息
 */
class Type extends Api
{
    protected $model = null;
    const API_URL = "/api/school/Type";

    protected $noNeedLogin = ['getList'];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\school\Type();
    }

    /**
     * @label 院校类别信息
     */
    public function getList(){
        $data = $this->model->order('id')->field('id,type_name as name')->select();
        $this->success('success',$data);
    }


}
