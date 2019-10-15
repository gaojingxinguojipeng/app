<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\library\Smsaaa as Smslib;
use app\common\model\User;
use think\Db;

/**
 * 手机短信接口
 */
class Sms extends Api
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';

    /**
     * 发送验证码
     *
     * @param string $mobile 手机号
     * @param string $event 事件名称
     */
    public function send()
    {
        $mobile = $this->request->request("mobile");
        $event = $this->request->request("event");
        $event = $event ? $event : 'register';

        if (!$mobile || !\think\Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('手机号不正确'));
        }
        $last = Smslib::get($mobile, $event);
        if ($last && time() - $last['createtime'] < 60) {
            $this->error(__('发送频繁'));
        }
        $ipSendTotal = \app\common\model\Sms::where(['ip' => $this->request->ip()])->whereTime('createtime', '-1 hours')->count();
        if ($ipSendTotal >= 5) {
            $this->error(__('发送频繁'));
        }
        if ($event) {
            $userinfo = User::getByMobile($mobile);
            if ($event == 'register' && $userinfo) {
                //已被注册
                $this->error(__('已被注册'));
            } elseif (in_array($event, ['changemobile']) && $userinfo) {
                //被占用
                $this->error(__('已被占用'));
            } elseif (in_array($event, ['changepwd', 'resetpwd']) && !$userinfo) {
                //未注册
                $this->error(__('未注册'));
            }
        }
        $ret = Smslib::send($mobile, null, $event);
        if ($ret) {
            $this->success(__('发送成功'));
        } else {
            $this->error(__('发送失败'));
        }
    }

    /**
     * 检测验证码
     *
     * @param string $mobile 手机号
     * @param string $event 事件名称
     * @param string $captcha 验证码
     */
    public function check()
    {
        $mobile = $this->request->request("mobile");
        $event = $this->request->request("event");
        $event = $event ? $event : 'register';
        $captcha = $this->request->request("captcha");

        if (!$mobile || !\think\Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('手机号不正确'));
        }
        if ($event) {
            $userinfo = User::getByMobile($mobile);

            if ($event == 'register' && $userinfo) {
                //已被注册
                $this->error(__('已被注册'));
            } elseif (in_array($event, ['changemobile']) && $userinfo) {
                //被占用
                $this->error(__('已被占用'));
            } elseif (in_array($event, ['changepwd', 'resetpwd']) && !$userinfo) {
                //未注册
                $this->error(__('未注册'));
            }
        }
        $ret = Smslib::check($mobile, $captcha, $event);

        if ($ret) {
            $userinfo=DB::table("fa_userinfo")->where(["user_mobile"=>$mobile])->find();
            if($userinfo['user_mobile']){
                $user_id=$userinfo['user_id'];
                $user_id=session('user_id',$user_id);
                $mobile=session('user_mobile',$mobile);
                $arr=[
                    'code'=>1,
                    'msg'=>"登录成功"
                ];
                return $arr;
            }else{
                $arr=DB::table("fa_userinfo")->insert(['user_mobile'=>$mobile]);
                $user_id=$userinfo['user_id'];
                $user_id=session(['user_id'=>$user_id,'user_mobile'=>$mobile]);
            }
        } else {
            $arr=[
                'code'=>2,
                'msg'=>"验证码不正确"
            ];
            return $arr;
        }
    }
}
