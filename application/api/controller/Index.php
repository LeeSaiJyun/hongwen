<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Db;
use think\Request;

/**
 * 首页接口
 */
class Index extends Api
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * api
     */
    public function index()
    {
        $this->success('请求成功');
    }

}
