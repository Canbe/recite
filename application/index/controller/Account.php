<?php
namespace app\index\controller;
use app\index\model\Words;
use app\index\model\User;
use app\index\controller\LoginBase;
use app\index\model\Assembly;
use app\index\model\Collect;
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
        $statistic = Words::getStatisticClassWords($userid);

        $statistic["recite"] = floor($statistic["already_Recite"]/$statistic["total"]*100);

        $this->assign("statistic",$statistic);
        $this->assign("recite",$statistic["recite"]);
        return view("account/summary");
    }

    //记忆模式
    public function memorize()
    {
        $num = input("num");
        $user = User::getLoginUser()[0];
        if(!$num)
        {
            $num = 0;
        }

        $page = 0;
        $pageLenght = 15;
        
        $list = Words::SelectWordList($user["id"],4,$page,$pageLenght,Common::getClassSql($user["level"]));

        $this->assign("list",$list);
        $this->assign("num",$num+1);
        $this->assign("memorize",[
            "total"=>$pageLenght
        ]);    
        return view("account/memorize");
    }

    public function memorize_record()
    {
        $user = User::getLoginUser()[0];
        $wordid = input("wordid");
        $score = input("score");

        if(!$wordid||!$score) return json(["status"=>"400"]);

        Words::MemorizeWords($user["id"],$wordid,$score);

        return json(["status"=>"200"]);
    }

    public function execise()
    {
        
    }

    //单词集合页面
    public function assembly()
    {
        $res = Collect::GetCollectList();

        $this->assign("list",$res);

        return view("account/assembly");
    }

    public function addassembly()
    {

        $name = input("name");
        $user = User::getLoginUser()[0];

        if(!$name||$name=="")
        {
            $this->error("参数错误","account/assembly",null,1);
        }

        Collect::Insert($name,$user["id"]);

        $this->redirect("account/assembly");
    }

    public function updateAssembly()
    {
        $name = input("name");
        $id = input("id");
        $user = User::getLoginUser()[0];

        if(!$id)
        {
            $this->error("参数错误","account/assembly",null,1);
        }

        Collect::UpdateCollect($id,$name);

        $this->redirect("enbook/assembly",["id"=>$id]);
    }

    public function addColected()
    {
        $listid = input("listid");
        $wordid = input("wordid");

        if(!$listid||!$wordid)
        {
            return json(["status"=>400]);
        }

        Collect::InsertCollected($listid,$wordid);

        return json(["status"=>200]);
    }
}