<?php

namespace app\api\controller;

use app\admin\model\Withdraw;
use app\common\controller\Api;
use Endroid\QrCode\QrCode;
use think\Response;
use think\Validate;

/**
 * 会员接口
 */
class User extends Api
{
    const API_URL = "/api/user";

    protected $noNeedLogin = [ 'login','share', 'mobilelogin'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * @label 获取用户提现列表
     */
    public function getWithdrawList() {
        $data =  Withdraw::field('id,money,balance,bank_id,withdrawtime,paytime,status')
            ->with(['banktext'])
            ->where(["user_id" => $this->auth->id])
            ->select();
        foreach ($data as $row => $value){
            if($value['withdrawtime'])
                $value['withdrawtime'] = date('Y-m-d H:i:s',$value['withdrawtime']);
            else
                $value['withdrawtime'] = null;
            if($value['paytime'])
                $value['paytime'] = date('Y-m-d H:i:s',$value['paytime']);
            else
                $value['paytime'] = null;
        }
        $this->success('success',$data);
    }

    /**
     * @label 绑定资料(已绑定不能修改)
     * @param realname:真实姓名
     * @param idcard:身份证
     * @param grade_id:年级ID
     * @param school_id:报读院校ID
     * @param major_id:报读专业ID
     */
    public function bindingdata(){
        $user = $this->auth->getUser();
        if ( $user->realname || $user->idcard ){
            $this->error('资料已经绑定，如要修改请联系管理员');
        }

        $realname = $this->request->request('realname');
        $idcard   = $this->request->request('idcard');
        $grade_id   = $this->request->request('grade_id/d');
        $school_id   = $this->request->request('school_id/d');
        $major_id   = $this->request->request('major_id/d');

        $validate = new Validate([
            'realname|姓名'  => 'require|max:25',
            'idcard|身份证' => 'require',
            'grade_id|年级' => 'require',
            'school_id|报读院校' => 'require',
            'major_id|报读专业' => 'require',
        ]);
        $data = [
            'realname'  => $realname,
            'idcard'  => $idcard,
            'grade_id'  => $grade_id,
            'school_id'  => $school_id,
            'major_id'  => $major_id,
        ];
        if (!$validate->check($data)) {
            $this->error($validate->getError());
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
     * @label 绑定手机号
     * @param telephone:手机号
     * @param captcha:短信验证码
     */
    public function bindingtelephone(){
        $user = $this->auth->getUser();

        $telephone = $this->request->request('telephone');
        $captcha = $this->request->request('captcha');
        if (!$telephone){
            $this->error('手机号不能为空');
        }
        if (!$captcha){
            $this->error(__('验证码不能为空'));
        }
        if (!Validate::regex($telephone, "^1\d{10}$")){
            $this->error(__('Mobile is incorrect'));
        }
        if (\app\common\model\User::where('mobile', $telephone)->where('id', '<>', $user->id)->find())
        {
            $this->error('手机号已存在');
        }
        try {
            \app\api\model\SmsAuth::checkAuth($telephone, $captcha);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $user->mobile = $telephone;
        $user->save();
        $this->success();
    }



    /**
     * @label 修改手机号
     * @param telephone:手机号
     * @param captcha:验证码
     */
    public function changemobile()
    {
        $user = $this->auth->getUser();
        $telephone = $this->request->request('telephone');
        $captcha = $this->request->request('captcha');
        if (!$telephone){
            $this->error('手机号不能为空');
        }
        if (!$captcha){
            $this->error(__('验证码不能为空'));
        }
        if (!Validate::regex($telephone, "^1\d{10}$")){
            $this->error(__('手机号不正确'));
        }
        if (\app\common\model\User::where('mobile', $telephone)->where('id', '<>', $user->id)->find())
        {
            $this->error('手机号已存在');
        }

        try {
            \app\api\model\SmsAuth::checkAuth($telephone, $captcha);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        $verification = $user->verification;
        $verification->mobile = 1;
        $user->verification = $verification;
        $user->mobile = $telephone;
        $user->save();

        $this->success();
    }

    /**
     * 获取分享二维码
     * @return Response
     */
    public function share(){
        $user = $this->auth->getUser();
        $qrCode = new QrCode();
        $qrCode->setText("pages/index/main?pid=".$user->openid)
            ->setSize(256)
            ->setPadding(12)
            ->setImageType(QrCode::IMAGE_TYPE_PNG);
//        $qrCode->setLogo(ROOT_PATH . 'public/assets/img/qrcode.png');
        //也可以直接使用render方法输出结果
//        $qrCode->render();
        return new Response($qrCode->get(), 200, ['Content-Type' => $qrCode->getContentType()]);
    }

}
