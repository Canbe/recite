<?php
namespace app\index\controller;
use app\index\model\Words;
use app\index\model\User;
use app\index\controller\LoginBase;
use app\index\model\Common;

class Account extends LoginBase{

    public function modify()
    {
        $name = input("name");
        $cee = input("cee");
        $cet4 = input("cet4");
        $cet6 = input("cet6");
        $pee = input("pee");
        $unrated = input("unrated");
        $level = 0;
        $level+=$cee+$cet4+$cet6+$pee+$unrated;
        $userid = User::getLoginUser()[0]["id"];

        if(!$name)
        {
            $this->error("参数错误","account/setting",null,3);
        }
        if(strlen($name)>20)
        {
            $this->error("名字过长","account/setting",null,3);
        }
        //$name = substr($name,0,10);       
        User::modify($userid,$name,$level);
        $this->redirect("account/setting");
    }

    public function setting()
    {
        $user = User::getLoginUser()[0];
        $user["levelName"] = Common::getClassString($user["level"],["CEE","CET4","CET6","PEE","SUMMIT"]);
        $this->assign("account",$user);
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