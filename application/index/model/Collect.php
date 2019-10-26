<?php
namespace app\index\model;
use think\Model;
use think\Db;

class Collect extends Model
{
    //新建一个收集
   public static function Insert($name,$userid)
   {
        $str = "insert into collect values(default,?,?)";

        return Db::query($str,[$name,$userid]);
   }

   //更新一个收集
   public static function UpdateCollect($id,$name)
   {
       $str = "update collect set 
       name = ? 
       where id = ?";

       return Db::query($str,[$name,$id]);
   }

   public static function GetCollectList()
   {
       $str = "select collect.id as id,name as name,userid as userid,count(*) as total
       from
       collect LEFT JOIN collected  on collect.id = collected.listid 
       GROUP BY collect.id";

       return Db::query($str);
   }

   public static function GetCollectById($id)
   {
       $str = "select collect.id as id,name as name,userid as userid,count(*) as total
       from
       collect LEFT JOIN collected  on collect.id = collected.listid 
       where collect.id = ? 
       GROUP BY collect.id ";
       return Db::query($str,[$id]);
   }

   //添加一个收集标记
   public static function InsertCollected($listid,$wordid)
   {
        $str = "insert IGNORE into collected values(?,?)";

        return Db::query($str,[$listid,$wordid]);
   }

    //删除一个收集标记
   public static function DeleteCollected($listid,$wordid)
   {
        $str = "delete from collected where listid = ? and wordid = ?";

        return Db::query($str,[$listid,$wordid]);
   }

   //从收集表中查询单词
   public static function GetWordListFromCollected($listid,$userid,$sort,$startpage,$pageLength)
   {
       if($sort==1)
       {
           $sort = 'id';
       }
       else
       {
           $sort = 'score';
       }
       $str = "select collected.wordid as id,en,trans,class,ifnull(record.score,0) as score,ifnull(record.userid,0) as userid
       from 
       collected left JOIN words on words.id = collected.wordid 
       left join record on record.wordid = collected.wordid and userid = ?
       where collected.listid =?
       ORDER BY ".$sort." limit ?,?;";

       return Db::query($str,[$userid,$listid,$startpage,$pageLength]);
   }
    
}