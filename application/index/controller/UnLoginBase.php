<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\User;

class UnLoginBase extends Controller{

    public function _initialize(){

        $this->assign("login",false);
        $this->assign("account","no user");
    }

    

}