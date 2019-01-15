<?php

namespace app\api\controller;

use app\admin\model\Withdraw;
use app\common\controller\Api;
use Endroid\QrCode\QrCode;
use fast\Random;
use think\Config;
use think\Request;
use think\Response;
use think\Validate;

/**
 * 会员接口
 */
class User extends Api
{
    const API_URL = "/api/user";

    protected $noNeedLogin = [ 'login', 'jscode2session', 'mobilelogin'];
    protected $noNeedRight = '*';

    public function _initialize(){
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
	 * @param type_id:类别
     * @param school_id:学校
     * @param cat_access_id:层次
     * @param major_id:专业
     */
    public function bindingdata(){
        $user = $this->auth->getUser();
        if ( $user->realname || $user->idcard ){
            $this->error('资料已经绑定，如要修改请联系管理员');
        }

        $realname = $this->request->request('realname');
        $idcard   = $this->request->request('idcard');
        $grade_id   = $this->request->request('grade_id/d');
        $type_id   = $this->request->request('type_id/d');
        $school_id   = $this->request->request('school_id/d');
        $cat_access_id   = $this->request->request('cat_access_id/d');
        $major_id   = $this->request->request('major_id/d');

        $validate = new Validate([
            'realname|姓名'  => 'require|max:25',
            'idcard|身份证' => 'require',
            'grade_id|年级' => 'require',
            'type_id|类别' => 'require',
        ]);
        $data = [
            'realname'  => $realname,
            'idcard'  => $idcard,
            'grade_id'  => $grade_id,
            'type_id'  => $type_id,
            'school_id'  => $school_id,
            'cat_access_id'  => $cat_access_id,
            'major_id'  => $major_id,
        ];
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }

        $user->realname = $realname;
        $user->idcard = $idcard;
        $user->grade_id = $grade_id;
        $user->type_id = $type_id;
        $user->school_id = $school_id;
        $user->cat_access_id = $cat_access_id;
        $user->major_id = $major_id;
        $user->save();
        $this->success('success',[$user->realname,$user->idcard]);
    }

	public function getData() {
		$data = db('user')
			->alias('u')
			->field('u.realname,u.idcard,u.grade_id,a.type_id,a.school_id,a.cat_access_id,a.major_id')
			->join('fa_application a','u.id=a.user_id','left')
			->order('a.id desc')
			->find($this->auth->id);
		$this->success('success',$data);
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
            $this->error('手机号不正确');
        }
        if (\app\common\model\User::where('mobile', $telephone)->where('id', '<>', $user->id)->find()){
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
        if (\app\common\model\User::where('mobile', $telephone)->where('id', '<>', $user->id)->find()){
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
     * @label 获取分享二维码
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

    /**
     * @label 重置密码
     *
     * @param newpassword:新密码
     * @param captcha:验证码
     */
    public function resetpwd(Request $request){
        $telephone = $this->auth->mobile;
        $newpassword = $this->request->request("newpassword");
        $captcha = $this->request->request("captcha");

        $validate = Validate::make([
            "newpassword|新密码" => "require|number|length:6,18",
            "captcha|验证码" => "require",
        ]);
        if (!$validate->check($request->param())) {
            $this->error($validate->getError());
        }

        //短信验证
        try {
            \app\api\model\SmsAuth::checkAuth($telephone, $captcha);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        //修改密码
        $salt = $this->auth->salt;
        $newpassword = $this->auth->getEncryptPassword($newpassword, $salt);
        $oldpassword = $this->auth->password;
        if($newpassword == $oldpassword){
            $this->error(__('新旧密码不能相同'));
        }

        $this->auth->getUser()->save(['password' => $newpassword]);
        $this->success(__('Reset password successful'));

        $this->error($this->auth->getError());

    }

	/**
	 * @label 登录凭证
	 * @param $code
	 */
	public function jscode2session($code=null) {
		if(!$code){
			$this->error("参数错误");
		}
		$appid = Config::get('wechat.sub_appid');
		$appsecret = Config::get('wechat.sub_appsecret');

		$url = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$appsecret}&js_code={$code}&grant_type=authorization_code";
		$output = CURL($url);
		if($output){
			$output = json_decode($output,true);
		}else{
			$this->error("请求微信错误");
		}
		if(array_key_exists('errcode',$output) && $output['errcode'] != 0 ){
			if(array_key_exists('errmsg',$output)){
				$this->error('微信响应错误',$output);
			}else{
				$this->error("请求微信错误");
			}
		}else{
			$this->success('成功',$output);
		}
	}


}
