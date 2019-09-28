<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
define("Translation_CURL_TIMEOUT",   10);
//翻译api地址
define("Translation_URL","http://api.fanyi.baidu.com/api/trans/vip/translate"); 
//替换为您的APPID
define("Translation_APP_ID","20190510000296039");
//替换为您的密钥
define("Translation_SEC_KEY","kaX4Rz5ovyhmTZ2zJrEW");
define("Compound_ACCESS_TOKEN","24.dff771b27e64ba0d163b71cd16acf32a.2592000.1571394778.282335-17274101");

function getClassSql($class)
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

        /*
    函数作用：获取类别字符串
    参数：$class为数字；$arrayName为类别数组
    返回值：类别字符串
    AUTHOR:朱永乐
    */ 
    function getClassString($class,$arrayName)
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