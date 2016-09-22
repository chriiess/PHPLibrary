<?php
/**
 * 字符截取 支持UTF8/GBK
 * @param $string
 * @param $length
 * @param $dot
 */
function strCut($string, $length, $dot = '...') {
    $strlen = strlen($string);
    if ($strlen <= $length) {
        return $string;
    }

    $string = str_replace(array(' ', '&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('∵', ' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
    $strcut = '';
    if (strtolower(CHARSET) == 'utf-8') {
        $length = intval($length - strlen($dot) - $length / 3);
        $n = $tn = $noc = 0;
        while ($n < strlen($string)) {
            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t <= 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }
            if ($noc >= $length) {
                break;
            }
        }
        if ($noc > $length) {
            $n -= $tn;
        }
        $strcut = substr($string, 0, $n);
        $strcut = str_replace(array('∵', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), array(' ', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
    } else {
        $dotlen = strlen($dot);
        $maxi = $length - $dotlen - 1;
        $current_str = '';
        $search_arr = array('&', ' ', '"', "'", '“', '”', '—', '<', '>', '·', '…', '∵');
        $replace_arr = array('&amp;', '&nbsp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;', ' ');
        $search_flip = array_flip($search_arr);
        for ($i = 0; $i < $maxi; $i++) {
            $current_str = ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
            if (in_array($current_str, $search_arr)) {
                $key = $search_flip[$current_str];
                $current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);
            }
            $strcut .= $current_str;
        }
    }
    return $strcut . $dot;
}

/**
 * 清除html、css、js格式并去除空格
 */
function ClearHtml($descclear) {

    $descclear = preg_replace("/<[ ]+/si", "<", $descclear); //过滤<__("<"号后面带空格)
    $descclear = preg_replace("/<\!--.*?-->/si", "", $descclear); //过滤html注释
    $descclear = preg_replace("/<(\!.*?)>/si", "", $descclear); //过滤DOCTYPE
    $descclear = preg_replace("/<(\/?html.*?)>/si", "", $descclear); //过滤html标签
    $descclear = preg_replace("/<(\/?head.*?)>/si", "", $descclear); //过滤head标签
    $descclear = preg_replace("/<(\/?meta.*?)>/si", "", $descclear); //过滤meta标签
    $descclear = preg_replace("/<(\/?body.*?)>/si", "", $descclear); //过滤body标签
    $descclear = preg_replace("/<(\/?link.*?)>/si", "", $descclear); //过滤link标签
    $descclear = preg_replace("/<(\/?form.*?)>/si", "", $descclear); //过滤form标签
    $descclear = preg_replace("/cookie/si", "COOKIE", $descclear); //过滤COOKIE标签
    $descclear = preg_replace("/<(applet.*?)>(.*?)<(\/applet.*?)>/si", "", $descclear); //过滤applet标签
    $descclear = preg_replace("/<(\/?applet.*?)>/si", "", $descclear); //过滤applet标签
    $descclear = preg_replace("/<(style.*?)>(.*?)<(\/style.*?)>/si", "", $descclear); //过滤style标签
    $descclear = preg_replace("/<(\/?style.*?)>/si", "", $descclear); //过滤style标签
    $descclear = preg_replace("/<(title.*?)>(.*?)<(\/title.*?)>/si", "", $descclear); //过滤title标签
    $descclear = preg_replace("/<(\/?title.*?)>/si", "", $descclear); //过滤title标签
    $descclear = preg_replace("/<(object.*?)>(.*?)<(\/object.*?)>/si", "", $descclear); //过滤object标签
    $descclear = preg_replace("/<(\/?objec.*?)>/si", "", $descclear); //过滤object标签
    $descclear = preg_replace("/<(noframes.*?)>(.*?)<(\/noframes.*?)>/si", "", $descclear); //过滤noframes标签
    $descclear = preg_replace("/<(\/?noframes.*?)>/si", "", $descclear); //过滤noframes标签
    $descclear = preg_replace("/<(i?frame.*?)>(.*?)<(\/i?frame.*?)>/si", "", $descclear); //过滤frame标签
    $descclear = preg_replace("/<(\/?i?frame.*?)>/si", "", $descclear); //过滤frame标签
    $descclear = preg_replace("/<(script.*?)>(.*?)<(\/script.*?)>/si", "", $descclear); //过滤script标签
    $descclear = preg_replace("/<(\/?script.*?)>/si", "", $descclear); //过滤script标签
    $descclear = preg_replace("/javascript/si", "Javascript", $descclear); //过滤script标签
    $descclear = preg_replace("/vbscript/si", "Vbscript", $descclear); //过滤script标签
    $descclear = preg_replace("/on([a-z]+)\s*=/si", "On\\1=", $descclear); //过滤script标签
    $descclear = preg_replace("/&#/si", "&＃", $descclear); //过滤script标签，如javAsCript:alert();
    //使用正则替换
    $pat = "/<(\/?)(script|i?frame|style|html|body|li|i|map|title|img|link|span|u|font|table|tr|b|marquee|td|strong|div|a|meta|\?|\%)([^>]*?)>/isU";
    $descclear = preg_replace($pat, "", $descclear);

    return $descclear;
}

/**
 * CURL GET
 */
function Curl_get($url) {
    //初始化
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 0);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //只需要设置一个秒的数量就可以
    curl_setopt($curl, CURLOPT_TIMEOUT, 60);
    //执行命令
    $data = curl_exec($curl);
    //关闭URL请求
    curl_close($curl);
    //显示获得的数据
    return $data;
}