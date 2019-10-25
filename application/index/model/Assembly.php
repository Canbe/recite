<?php
namespace app\index\model;
use think\Model;
use think\Db;

class Assembly extends Model
{
    
    public static function Insert($userid,$name,$link)
    {
        $str = "insert into assembly values (default,?,?,?,curdate(),0)";

        return Db::query($str,[$name,$userid,$link]);
    }

    public static function UpdateAssembly($id,$name,$link)
    {
        $str = "update assembly set 
        name = ?,
        link = ? 
        where id = ?";

        return Db::query($str,[$name,$link,$id]);
    }

    public static function GetAssemblyList()
    {
        $str = "select * from assembly";

        return Db::query($str);
    }

    public static function GetAssemblyById($id)
    {
        $str = "select * from assembly where id = ?";

        return Db::query($str,[$id]);
    }

    public static function UpdateAssemblyAboutTime($id,$score)
    {
        $str = "update assembly set score = if(curdate()=lastdate,score+?,score div 2 + 1),lastdate=curdate() where id=?";

        return Db::query($str,[$score,$id]);
    }

}