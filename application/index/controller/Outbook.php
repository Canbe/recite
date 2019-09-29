<?php
namespace app\index\controller;
use app\index\model\Words;
use app\index\model\User;
use app\index\controller\UnLoginBase;
use app\index\model\Translation;
use app\index\model\Common;

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
        $this->assign("PopularList",Words::SelectWordScore());

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
            $vo[0]["className"] = Common::getClassString($vo[0]["class"],["CEE","CET4","CET6","PEE","SUMMIT"]);

            Words::UpdateWordAboutTime($vo[0]["id"],1);

            $link_out = [];

            $index = 0;

            if(file_exists("static/mp3/".$search_word."_103.mp3"))
            {
                //nothing to do
            }
            else
            {
                Common::DownloadMP3($search_word,"103");
            }

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

    public function translation($q,$from,$to)
    {
         print_r(Translation::translate($q,$from,$to));
    }

    private function getAccessToken()
    {

        //证书无效，使用http连接。
        $url ="https://openapi.baidu.com/oauth/2.0/token";
        $arg = 
        ["grant_type"=>"client_credentials",
        "client_id"=>"ULVbD5cs5LQd5Bcrv6A6kQQW","client_secret"=>"2vWuW2oT4VUnDYDM6mjz9StZZt5EtZyT"
        ];

        $res = Translation::callOnce($url,$arg,"get");
        print_r(json_decode($res,true));
    }

    public function fortest()
    {
        print_r(Common::getClassSql(1100));
    }
    
}