<?php
namespace app\index\model;
use think\Model;
use think\Db;
use think\Session;

class User extends Model
{
    public static function getUser($account){
        $str = "select * from user where account = ?";

        return Db::query($str,[$account]);
    }

    public static function login($account,$password)
    {
        $res = User::getUser($account);

        if(!$res||$res[0]["password"]!=$password||$res[0]["permit"]<0)
        {
            return false;
        }
        Session::set("ACCOUNT",$account);
        Session::set("ACCOUNTID",$res[0]["id"]);
        return true;
    }

    public static function HasLogin()
    {
        if(Session::has("ACCOUNT"))
        {
            return true;
        }
        return false;
    }

    public static function getLoginUser()
    {
        return User::getUser(Session::get("ACCOUNT"));
    }

    public static function exit()
    {
        Session::delete("ACCOUNT");
    }

    public static function GetUserNotZeroWordCount($userid)
    {
        $str = "select count(*) as recite from words left join record on words.id = record.wordid and userid = ? where record.score != 0";
        return Db::query($str,[$userid])[0]["recite"];
    }

    public static function UpdateUserLastime($userid,$score)
    {
        $str = "insert into user_history values (DEFAULT,?,?,now(),curdate()) ON DUPLICATE KEY UPDATE lasttime = now() , score = score + ? ";

        return Db::query($str,[$userid,$score,$score]);
    }

    public static function SelectUserLastime($num)
    {
        $str = "select user.account,user.name,user_history.date,user_history.score from user_history left join user on user.id = user_history.userid order by user_history.date DESC,score DESC limit ?";
        return Db::query($str,[$num]);
    }

    public static function modify($id,$name,$level)
    {
        $str = "update user set name = ?,level = ? where id = ? ";
        return Db::query($str,[$name,$level,$id]);
    }

    public static function InsertNewUser($account,$name,$password)
    {
        $str = "insert into user (id,name,password,lasttime,account,level,permit) values(default,?,?,now(),?,0,-1)";

        Db::query($str,[$name,$password,$account]);
    }
    
}