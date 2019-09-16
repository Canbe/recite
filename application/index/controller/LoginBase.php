<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\User;

class LoginBase extends Controller{

    public function _initialize(){


        if(!User::HasLogin())
        {
            $this->error("请先登录","login/index",null,3);
        }

        $this->assign("login",true);
        $this->assign("account",User::getLoginUser()[0]);
        

    }
    

}