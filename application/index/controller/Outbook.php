<?php
namespace app\index\controller;
use think\Db;
use think\Session;
use app\index\model\Words;
use app\index\model\User;
use app\index\controller\UnLoginBase;

class Outbook extends UnLoginBase
{

    public function index(){

        if(User::HasLogin())
        {
            $this->assign("login",true);
            $this->assign("account",User::getLoginUser()[0]);
        }

        $res = User::SelectUserLastime(10);

        $this->assign("UserHistoryList",$res);

        return view("outbook/index");

    }

    //访问en页面
    public function en(){

        $en = input("en");

        if(!$en)
        {
            $en = "nothing";
        }

        

        return $this->word($en);  
    }

    public function word($en){
        //查询单词

        $account = "no user";
        $login = false;

        if(User::HasLogin())
        {
            $account = User::getLoginUser()[0];
            $login = true;           
        }

        $this->assign("login",$login);
        $this->assign("account",$account);

        if($login)
        {
            $vo = Words::SelectWordWithUser($en,$account["id"]);
        }
        else
        {
            $vo = Words::SelectWord($en);
        }
        
        if($vo)
        {
            $search_word = $vo[0]["en"];
            $links = explode(';',$vo[0]["link"]);

            $link_out = [];

            $index = 0;

            foreach($links as $key => $linkword)
            {
                if($linkword == "")
                {
                    continue;
                }

                if(substr($linkword,0,1)=='#')
                {

                    $res = Words::likeSelect(substr($linkword,1),10);


                    foreach($res as $likeRes)
                    {
                        if($search_word!=$likeRes["en"])
                        {
                            $link_out[$index++] = $likeRes;
                        }
                        
                    }


                }
                else
                {
                    $word = Words::SelectWord($linkword);
                    if($word)
                    {
                        $link_out[$index++] = $word[0];
                    }
                }             
            }
            $this->assign("find",1);
            $this->assign("vo",$vo[0]);
            $this->assign("links",$link_out);
            
        }
        else
        {
            if($en=='nothing')
            {
                $link_out = Words::randomSelect(15);
            }
            else
            {
                $link_out = Words::likeSelect($en,15);
            }

            $this->assign("find",0);
            $this->assign("links",$link_out);

        }

        return view("outbook/en");
    }

    //模糊查询
    public function fuzzyquery(){
        $en = input("en");

        if(!$en)
        {
            $en = '';
        }
        $res = Words::likeSelect($en,8);
        return json($res);
    }

    public function havenot()
    {
        $this->error("该功能暂未开发","outbook/index",null,4);
    }

    public function fortest()
    {

    }
}