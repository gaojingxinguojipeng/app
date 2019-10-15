<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use think\Db;
class Login extends Frontend
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    //登录页面
    public function login(){
        return $this->view->fetch('login/login');
    }

}