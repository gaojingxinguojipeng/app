<?php

namespace app\api\controller;

use app\common\controller\Api;


/**
 * 会员接口
 */
class Text extends Api
{
    protected $noNeedLogin = ['testLogin', 'mobilelogin', 'register', 'resetpwd', 'changeemail', 'changemobile', 'third'];
    protected $noNeedRight = '*';

    public function testLogin()
    {



    }
}
