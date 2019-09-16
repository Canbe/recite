<?php
namespace app\index\controller;
use app\index\model\Words;
use app\index\model\User;
use app\index\controller\LoginBase;

class Account extends LoginBase{

    public function modify()
    {
        $name = input("name");
        $level = input("level");
        $userid = User::getLoginUser()[0]["id"];

        if(!$name)
        {
            $this->error("参数错误","account/setting",null,3);
        }
        //$name = substr($name,0,10);       
        User::modify($userid,$name,$level);
        $this->redirect("account/setting");
    }

    public function setting()
    {
        return view("account/setting");
    }

        //总结模式
        public function summary()
        {
            $userid = User::getLoginUser()[0]["id"];
            $statistic = Words::getStatisticClassWords();
            $recite = User::GetUserNotZeroWordCount($userid);
    
            $this->assign("statistic",$statistic);
            $this->assign("recite",$recite);
            return view("account/summary");
        }

}