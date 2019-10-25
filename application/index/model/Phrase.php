<?php
namespace app\index\model;
use think\Model;
use think\Db;

class Phrase extends Model
{
    public static function Insert($en,$trans)
    {
        $str = "insert into phrase values(default,?,?,now(),'')";

        return Db::query($str,[$en,$trans]);
    }

    public static function Modify($id,$en,$trans)
    {
        $str = "update phrase set       
            en = ?,
            trans = ? 
            where id = ? ";
        
        return Db::query($str,[$en,$trans,$id]);
    }

    public static function GetPhraseList($page,$length,$sort = 3)
    {
        switch($sort)
        {
            case 1:$sort = "id";
            break;
            case 3:$sort = "en";
            break;
            default:
            $sort = 'id';
        }

        $str = "select * from phrase order by ".$sort." limit ?,?";

        return Db::query($str,[$page,$length]);
    }

    public static function GetTotal(){
        $str = "select count(*) as total from phrase ";

        return Db::query($str)[0]["total"];
    }
    
}