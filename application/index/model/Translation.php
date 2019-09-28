<?php
namespace app\index\model;

class Translation
{


    //翻译入口
    public static function translate($query, $from, $to)
    {
        $args = array(
            'q' => $query,
            'appid' => Translation_APP_ID,
            'salt' => rand(10000,99999),
            'from' => $from,
            'to' => $to,

        );
        $args['sign'] = Translation::buildSign($query, Translation_APP_ID, $args['salt'], Translation_SEC_KEY);
        //发起请求
        $ret = Translation::call(Translation_URL, $args);
        //解析请求
        $ret = json_decode($ret, true);
        return $ret; 
    }

    /**
     * 按要求加密函数
     */
    public static function buildSign($query, $appID, $salt, $secKey)
    {/*{{{*/
        $str = $appID . $query . $salt . $secKey;
        $ret = md5($str);
        return $ret;
    }/*}}}*/

    //发起网络请求
    public static function call($url, $args=null, $method="post", $testflag = 0, $timeout = Translation_CURL_TIMEOUT, $headers=array())
    {/*{{{*/
        $ret = false;
        $i = 0; 
        while($ret === false) 
        {
            if($i > 1)
                break;
            if($i > 0) 
            {
                sleep(1);
            }
            $ret = Translation::callOnce($url, $args, $method, false, $timeout, $headers);
            $i++;
        }
        return $ret;
    }/*}}}*/

    public static function callOnce($url, $args=null, $method="post", $withCookie = false, $timeout = Translation_CURL_TIMEOUT, $headers=array())
    {/*{{{*/
        $ch = curl_init();
        if($method == "post")
        {
            $data = Translation::convert($args);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        else 
        {
            $data = Translation::convert($args);
            if($data) 
            {
                if(stripos($url, "?") > 0) 
                {
                    $url .= "&$data";
                }
                else 
                {
                    $url .= "?$data";
                }
            }
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if(!empty($headers)) 
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if($withCookie)
        {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $_COOKIE);
        }
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }/*}}}*/

    public static function convert(&$args)
    {/*{{{*/
        $data = '';
        if (is_array($args))
        {
            foreach ($args as $key=>$val)
            {
                if (is_array($val))
                {
                    foreach ($val as $k=>$v)
                    {
                        $data .= $key.'['.$k.']='.rawurlencode($v).'&';
                    }
                }
                else
                {
                    $data .="$key=".rawurlencode($val)."&";
                }
            }
            return trim($data, "&");
        }
        return $args;
    }/*}}}*/
}