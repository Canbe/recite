<?php
namespace app\index\controller;
use think\Db;
use think\Session;
use app\index\model\Words;
use app\index\model\User;
use app\index\controller\LoginBase;
use app\index\model\Assembly;
use app\index\model\Collect;
use app\index\model\Common;
use app\index\model\Phrase;

class Enbook extends LoginBase
{

    //访问列表页面
    public function list(){

        $page = input("page",1);
        $sort = input("sort");
        $user = User::getLoginUser()[0];
        $id = input("id",0);

        if($sort)
        {
            Session::set("SORT",$sort);
        }
        $sort = Session::get("SORT");
        //页面单词数
        $pageLength = 15;
        if($id>0)
        {
            return $this->GetAssemblyWordsList($user,$sort,$page,$pageLength,$id);
        }
        else if($id==-1)
        {
            return $this->GetScoreWordsList($user,$sort,$page,$pageLength,1);
        }
        return $this->GetScoreWordsList($user,$sort,$page,$pageLength,0);
    }

    private function GetAssemblyWordsList($user,$sort,$page,$pageLength,$id)
    {
        $assembly = Collect::GetCollectById($id);

        if(!$assembly)
        {
            $this->error("指定页面不存在了","account/assembly",null,2);
        }
        $total = $assembly[0]["total"];
        $totalpage = ceil($total/15);
       
        if($page<1)
        {
            $page = 1;
        }
        else if($page>$totalpage)
        {
            $page = $totalpage;
        }
        //起始单词
        $startWord = ($page-1)*15;

        $list = Collect::GetWordListFromCollected($id,$user["id"],$sort,$startWord,$pageLength);
        $this->assign("assembly",$assembly[0]);
        $this->assign("list",$list);
        $this->assign("page",$page);
        $this->assign("totalpage",$totalpage);

        return view("enbook/list");
    }

    //获得用户得分单词列表
    private function GetScoreWordsList($user,$sort,$page,$pageLength,$tab)
    {      
        if($tab==1)
        {
            $total = Words::getSelectWordListTotal($user["id"],$sort,0,$tab)[0]["total"];
        }
        else
        {
            $total = Words::getSelectWordListTotal($user["id"],$sort,$user["level"],$tab)[0]["total"];
        }
        $totalpage = ceil($total/$pageLength);
        if($page<1)
        {
            $page = 1;
        }
        else if($page>$totalpage)
        {
            $page = $totalpage;
        }
        //起始单词
        $startWord = ($page-1)*15;
        if($tab==1)
        {
            $list = Words::SelectWordList($user["id"],$sort,$startWord,$pageLength,'true',$tab);
            $this->assign("assembly",["id"=>-$tab,"name"=>"Phrase"]);
        }
        else
        {
            $level = Common::getClassSql($user['level']);
            $list = Words::SelectWordList($user["id"],$sort,$startWord,$pageLength,$level,$tab);
            $this->assign("assembly",["id"=>-$tab,"name"=>"List"]);
        }


        $this->assign("list",$list);
        $this->assign("page",$page);
        $this->assign("totalpage",$totalpage);


        return view('enbook/list');
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

    public function removeCollect()
    {
        $id = input("id",0);
        $wordid = input("wordid",0);
        $page = input("page",0);
        $user = User::getLoginUser()[0];


        if($user["permit"]<2)
        {
            $this->error("权限不足","enbook/list",["id"=>$id,"page"=>$page],3);
        }

        if($id!=0)
        {
            Collect::DeleteCollected($id,$wordid);
        }
        return $this->redirect("enbook/list",["id"=>$id,"page"=>$page]);
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
        $tab = input("tab",0);
        $class = 0;
        $user = User::getLoginUser()[0];

        if($tab==0)
        {
            $class+=$cee+$cet4+$cet6+$pee+$unrated;
        }
        else
        {
            $tab = 1;
            $sentence="";
        }
        if($user["permit"]<=0)
        {
            $this->error("权限不足","outbook/index",null,2);
        }
        Words::UpdateWord($id,$en,$trans,$sentence,$link,$class,$tab);
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
        $tab = input("tab",0);
        $class = 0;
        if($tab==0)
        {
            $class+=$cee+$cet4+$cet6+$pee+$unrated;
        }
        else
        {
            $sentence = '';
        }

        if($user["permit"]<=1)
        {
            $this->error("权限不足","outbook/index",null,2);
        }

        if($en&&$en!="")
        {
            Words::InsertWord($en,$trans,$sentence,$class,$tab);
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
        $collect = input("collect",0);
        $size = input("size");
        $user = User::getLoginUser()[0];
        $pageLenght = 40;
        
        if(!$size)
        {
            $pageLenght = 40;
        }
        else
        {
            $pageLenght = $size;
        }
        

        //测试特定的收集包
        if($collect!=0)
        {
            return $this->CollectTest($user["id"],$collect,$pageLenght);
        }
        return $this->NormalTest($user,$pageLenght);
        
    }

    

    //考试模式
    public function exam()
    {
        $list = Words::randomSelect(40);

        $this->assign("list",$list);
        return view("exam");
    }

    

    private function CollectTest($userid,$listid,$pageLenght)
    {
        $list = Collect::GetWordListFromCollected($listid,$userid,2,0,$pageLenght);

        return $this->GetTestView($pageLenght,$list);
    }

    private function NormalTest($user,$pageLenght)
    {
        $list = Words::SelectWordList($user["id"],2,0,$pageLenght,Common::getClassSql($user['level']),0);
        return $this->GetTestView($pageLenght,$list);
    }

    private function GetTestView($pageLenght,$list)
    {
        shuffle($list);
        $this->assign("pageLenght","$pageLenght");
        $this->assign("list",$list);
        if(Common::ismobile())
        {
            return view("enbook/mobiletest");
        }
        return view("enbook/test");
    }

    

    


}
