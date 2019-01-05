<?php

namespace app\api\controller;

use app\admin\model\Bank;
use app\common\controller\Api;
use think\Db;
use think\Validate;

/**
 * 提现
 */
class Withdraw extends Api
{
    const API_URL = "/api/Withdraw";

    protected $model;

    protected $noNeedLogin = [''];
    protected $noNeedRight = '*';

    public function _initialize(){
        parent::_initialize();
        $this->model = new \app\admin\model\Withdraw();

    }

    /**
     * @label 提现
     * @param money:提现金额
     * @param bank_id:银行卡ID
     * @param password:支付密码
     */
    public function create(){
        $money = $this->request->param('money');
        $bank_id = $this->request->param('bank_id');
        $password = $this->request->param('password');

        $validate = new Validate([
            'money|提现金额'  => 'require|float|>=:1',
            'bank_id|提现银行卡' => 'require',
            'password|支付密码' => 'require',
        ]);
        $data = [
            'money' => $money,
            'bank_id' => $bank_id,
            'password' => $password,
        ];
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }
        //支付密码校验
        if($this->auth->password != $this->auth->getEncryptPassword($password, $this->auth->salt)){
            $this->error('支付密码错误');
        };

        //检查可提现余额
        $balance = $this->auth->balance - $money;
        if($balance<0){
            $this->error('可提现余额'.$this->auth->balance.'元');
        }

        //检查当前银行卡用户
        $bank = Bank::getOneCard($this->auth->id,$bank_id);
        if(!$bank){
            $this->error('银行卡不存在');
        }

        //开启事务
        Db::startTrans();
        try{
            //创建提现记录
            $user = \app\admin\model\Withdraw::create([
                'money'=>$money,
                'balance'=>$balance,
                'user_id'=>$this->auth->id,
                'bank_id' => $bank_id,
                'withdrawtime'=>time(),
                'status'=>0,
            ]);
            //转移到冻结金额
            $change = \app\common\model\User::balanceToFrozen($this->auth->id,$money);
            if($change && $user){
                Db::commit(); //提交
                $this->success('success');
            }else{
                Db::rollback();//回滚
                $this->error("unknown error");
            }

        }catch (Exception $e){
            Db::rollback();
            $this->error($e->getMessage());
        }

    }
}
