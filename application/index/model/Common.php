<?php
namespace app\index\model;
use think\Model;
use think\Db;

class Common extends Model
{
    public static function getOrder($num)
    {
        if($num=='1')
        {
            return "words.id";
        }
        else if($num=='2')
        {
            return "score";
        }
        else if($num=='3')
        {
            return "words.en";
        }
        return " relast,score ";
        
    }

    public static function ismobile()
    {

        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备

        if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))

            return true;

    

        //此条摘自TPM智能切换模板引擎，适合TPM开发

        if (isset ($_SERVER['HTTP_CLIENT']) && 'PhoneClient' == $_SERVER['HTTP_CLIENT'])

            return true;

        //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息

        if (isset ($_SERVER['HTTP_VIA']))

            //找不到为flase,否则为true

            return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;

        //判断手机发送的客户端标志,兼容性有待提高

        if (isset ($_SERVER['HTTP_USER_AGENT'])) {

            $clientkeywords = array(

                'nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile'

            );

            //从HTTP_USER_AGENT中查找手机浏览器的关键字

            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {

                return true;

            }

        }

        //协议法，因为有可能不准确，放到最后判断

        if (isset ($_SERVER['HTTP_ACCEPT'])) {

            // 如果只支持wml并且不支持html那一定是移动设备

            // 如果支持wml和html但是wml在html之前则是移动设备

            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {

                return true;

            }

        }

        return false;

    }

    public static function GenerateMP3($name,$data)
    {
        $name = "static/mp3/".$name.".mp3";
        $file = fopen($name, "w");      
        fwrite($file, $data);
        fclose($file);
    }

    public static function DownloadMP3($tex,$per)
    {
        $url = "http://tsn.baidu.com/text2audio";
        $arg = [
            "tex" => $tex,
            "lan" => "zh",
            "cuid" => "7C2A31503AB6",
            "ctp" => "1",
            "tok" => Compound_ACCESS_TOKEN,
            "per" =>$per
        ];


        $res = Translation::call($url,$arg,"post");

        Common::GenerateMP3($tex."_".$per,$res);
    }

    public static function getTodaySentence()
    {
        $url = "http://api.ooopn.com/ciba/api.php";
        $arg =[];
        $res = Translation::call($url,$arg,"get");
        return $res;
    }

        /*
    函数作用：获取类别字符串
    参数：$class为数字；$arrayName为类别数组
    返回值：类别字符串
    AUTHOR:朱永乐
    */ 
    public static function getClassString($class,$arrayName)
    {
        $string = "";
        if ($class == NULL || $class == "") {
        return "UNRATED";
        }
        for ($j=0; $j<count($arrayName);$j++) { 
        if($class%10 == 1)
        { 
            $string = $string.$arrayName[$j]." | ";
        }
        $class = $class/10;
        }
        return chop($string," | ");
    }

    public static function getClassSql($class)
    {
        $string = "";
        if($class==NULL || $class == "" || $class == 0)
        {
            return "false";
        }

        for($i = 1;$i<=10000;$i=$i*10)
        {
            if($class%10==1)
            {
                $string = $string."class div $i%10=1 or ";
            }
            $class = $class/10;
        }
        return chop($string," or ");
    }
}