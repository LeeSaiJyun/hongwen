<?php

namespace app\api\controller;

use app\admin\model\Withdraw;
use app\common\controller\Api;
use app\common\library\Sms;
use fast\Random;
use think\Validate;

/**
 * 会员接口
 */
class User extends Api
{

    protected $noNeedLogin = [ 'login', 'mobilelogin', 'register', 'changemobile'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 会员中心
     */
    public function index()
    {
        $this->success('', ['welcome' => $this->auth->nickname]);
    }

    /**
     * 会员登录
     *
     * @param string $account 账号
     * @param string $password 密码
     */
    public function login()
    {
        $account = $this->request->request('account');
        $password = $this->request->request('password');
        if (!$account || !$password)
        {
            $this->error(__('Invalid parameters'));
        }
        $ret = $this->auth->login($account, $password);
        if ($ret)
        {
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Logged in successful'), $data);
        }
        else
        {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 获取用户提现列表
     */
    public function getWithdrawList() {
        $data =  Withdraw::all(["user_id" => $this->auth->id]);

        $this->success('success',$data);
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        $this->auth->logout();
        $this->success(__('Logout successful'));
    }


    /**
     * 绑定资料(已绑定不能修改)
     *
     * @apiParam $realname  string  真实姓名
     * @apiParam $idcard    string  身份证
     * @apiParam $grade_id  int     年级ID
     * @apiParam $school_id int     报读院校ID
     * @apiParam $major_id  int     报读专业ID
     */
    public function binding()
    {
        $user = $this->auth->getUser();

        $realname = $this->request->request('realname');
        $idcard   = $this->request->request('idcard');
        $grade_id   = $this->request->request('grade_id');
        $school_id   = $this->request->request('school_id');
        $major_id   = $this->request->request('major_id');

        if ( $user->realname || $user->idcard )
        {
            $this->error('资料已经存在');
        }

        $user->realname = $realname;
        $user->idcard = $idcard;
        $user->grade_id = $grade_id;
        $user->school_id = $school_id;
        $user->major_id = $major_id;
        $user->save();
        $this->success('success',[$user->realname,$user->idcard]);
    }



    /**
     * 修改手机号
     * 
     * @param string $email 手机号
     * @param string $captcha 验证码
     */
    public function changemobile()
    {
        $user = $this->auth->getUser();
        $mobile = $this->request->request('mobile');
        $captcha = $this->request->request('captcha');
        if (!$mobile || !$captcha)
        {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::regex($mobile, "^1\d{10}$"))
        {
            $this->error(__('Mobile is incorrect'));
        }
        if (\app\common\model\User::where('mobile', $mobile)->where('id', '<>', $user->id)->find())
        {
            $this->error(__('Mobile already exists'));
        }
        $result = Sms::check($mobile, $captcha, 'changemobile');
        if (!$result)
        {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verification->mobile = 1;
        $user->verification = $verification;
        $user->mobile = $mobile;
        $user->save();

        Sms::flush($mobile, 'changemobile');
        $this->success();
    }


}
