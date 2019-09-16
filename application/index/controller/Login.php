<?php
namespace app\index\controller;
use app\index\controller\UnLoginBase;
use app\index\model\Common;
use app\index\model\User;
use think\Session;

class Login extends UnLoginBase
{
    public function index()
    {
        return view("index");
    }

    public function newuser()
    {
        return view("newuser");
    }

    public function login()
    {
        $account = input("account");
        $password = input("password");
        
        if(!$account||!$password||!User::login($account,$password))
        {
            $this->error("请填写正确的账号密码","login/index",null,4);
        }
        User::UpdateUserLastime(User::getLoginUser()[0]["id"],0);
        $this->redirect("outbook/index");
    }

    public function exit()
    {
        User::exit();
        $this->success("已退出登录","login/index",null,3);
    }

    public function register()
    {
        $this->error("暂不支持注册新用户，敬请期待","login/index",null,4);
    }

    public function help()
    {
        if(User::HasLogin())
        {
            $this->assign("login",true);
            $this->assign("account",User::getLoginUser()[0]);
        }
        return view("help");
    }
  
}