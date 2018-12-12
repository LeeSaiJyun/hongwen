<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use app\common\library\Token;
use think\Request;

class Index extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function _initialize(){
        parent::_initialize();
    }

    public function index(Request $request){
        $this->redirect($request->domain() . '/chickenleg.html');        //重定向
    }
}
