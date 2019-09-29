<?php
namespace app\index\controller;
use think\Db;
use think\Session;
use app\index\model\Words;
use app\index\model\User;
use app\index\controller\LoginBase;
use app\index\model\Common;

class Enbook extends LoginBase
{

    public function list(){

        $page = input("page");
        $sort = input("sort");
        $user = User::getLoginUser()[0];

        if($sort)
        {
            Session::set("SORT",$sort);
        }

        $total = Words::getSelectWordListTotal($user["id"],$sort,$user["level"])[0]["total"];
        $totalpage = ceil($total/15);
        $sort = Session::get("SORT");

        if(!$sort)
        {
            $sort = 2;
        }

        //页面单词数
        $pageWord = 15;

        if(!$page||$page<1)
        {
            $page = 1;
        }
        else if($page>$totalpage)
        {
            $page = $totalpage;
        }

        $lastWord = ($page-1)*15;
        $level = Common::getClassSql($user['level']);

        $list = Words::SelectWordList($user["id"],$sort,$lastWord,$pageWord,$level);

        $this->assign("list",$list);
        $this->assign("page",$page);
        $this->assign("totalpage",$totalpage);
        $this->assign("sort",$sort);

        return view('list');
    }

    //修改玩家对某单词的分数
    public function changeScore(){

        $wordid = input("id");
        $score = input("score");
        $userid = User::getLoginUser()[0]["id"];
        if(!$wordid||!$score||!$userid)
        {
            return json(["status"=>400]);
        }
        $result = Words::UpdateRelativeRecord($wordid,$userid,$score);
        if($score>0)
        {
            User::UpdateUserLastime($userid,1);
        }
        else if($score<0)
        {
            Words::UpdateWordAboutTime($wordid,5);
        }
        return json(["status"=>200]);
    }

    //修改单词
    public function modify(){
        $id = input("id");
        $en = input("en");
        $trans = input("trans");
        $sentence = input("sentence");
        $link = input("link");
        $cee = input("cee");
        $cet4 = input("cet4");
        $cet6 = input("cet6");
        $pee = input("pee");
        $unrated = input("unrated");
        $class = 0;
        $class+=$cee+$cet4+$cet6+$pee+$unrated;
        $user = User::getLoginUser()[0];
        if($user["permit"]<=0)
        {
            $this->error("权限不足","outbook/index",null,4);
        }
        Words::UpdateWord($id,$en,$trans,$sentence,$link,$class);
        $this->redirect("outbook/en",["en"=>$en]);
    }

    //添加新单词
    public function add(){
        $en = input("en");
        $trans = input("trans");
        $sentence = input("sentence");
        $user = User::getLoginUser()[0];

        $cee = input("cee");
        $cet4 = input("cet4");
        $cet6 = input("cet6");
        $pee = input("pee");
        $unrated = input("unrated");
        $class = 0;
        $class+=$cee+$cet4+$cet6+$pee+$unrated;

        if($user["permit"]<=1)
        {
            $this->error("权限不足","outbook/index",null,4);
        }

        if($en&&$en!="")
        {
            Words::InsertWord($en,$trans,$sentence,$class);
        }
        $this->redirect("outbook/en",["en"=>$en]);
    }

    //检测单词是否存在
    public function isExist()
    {
        $en = input("en");
        if(!$en)
        {
            return json(["status"=>200]);
        }
        $res = Words::SelectWord($en);
        if(!$res)
        {
            return json(["status"=>400]);
        }
        return json(["status"=>200]);
    }

    //测试模式
    public function test(){
        $user = User::getLoginUser()[0];
        $list = Words::SelectWordList($user["id"],2,0,40,Common::getClassSql($user['level']));

        shuffle($list);

        $this->assign("list",$list);
        if(Common::ismobile())
        {
            return view("enbook/mobiletest");
        }
        return view("enbook/test");
    }

    //考试模式
    public function exam()
    {
        $list = Words::randomSelect(40);

        $this->assign("list",$list);
        return view("exam");
    }

    


}
