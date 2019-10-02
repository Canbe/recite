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
        $this->success("已退出登录","login/index",null,1);
    }

    public function register()
    {
        $name = input("name");
        $email = input("email");
        $password = input("password");
        $account = input("account");
        $Repassword = input("Re-password");


        if(!$name||!$email||!$password||!$account||$password!=$Repassword)
        {
            $this->success("参数空缺或者两次密码不一致","login/newuser",null,2);
        }

        User::InsertNewUser($account,$name.'#'.$email,$password);


        $this->redirect("login/welcome",["account"=>$account,"email"=>$email,"name"=>$name]);

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

    public function welcome()
    {
        $name = input("name");
        $email = input("email");
        $account = input("account");

        if(!$account)
        {
            $account = "unknow";
            $name ="nobody";
            $email = "nobody@alien.com";
        }

        $this->assign("name",$name);
        $this->assign("email",$email);
        $this->assign("account",$account);
        return view("login/welcome");
    }
  
}