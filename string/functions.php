<?php

/**
 * #######################################
 *
 * 字符串处理相关代码片段
 *
 * #######################################
 */

/**
 * 等宽截取字符串函数
 * @description
 *  一个中文字符代表两个长度,一个英文字符代表一个长度
 * @author gwing
 * @since 2016-09-02
 * @param string $str 要截取的字符串
 * @param int $length 要截取的长度
 * @param int $start 要截取的起始位置
 * @param string $suffix 自定义后缀
 * @return string $new_str 返回截取的字符串
 */

function msubstr($str, $length, $start = 0, $suffix = "")
{

    $new_str = "";
    $str_length = $length + $start;

    for ($i = $start; $i < $str_length; $i++) {

        if (ord(mb_substr($str, $i, 1, 'utf-8')) > 0xa0) {
            $str_length -= 1;
        }

        $new_str .= mb_substr($str, $i, 1, 'utf-8');
    }

    return $new_str . $suffix;
}

/**
 * 产生随机字符串
 *
 * @param    int        $length  输出长度
 * @param    string     $chars   可选的 ，默认为 0123456789
 * @return   string     字符串
 */
function random($length, $chars = '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ')
{
    $hash = '';
    $max = strlen($chars) - 1;
    for ($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}

/**
 * 截取中文UTF-8字符串
 *
 * @param string $str   要截取的字符串
 * @param string $start 中文UTF-8字符串的起始位置
 * @param int    $lenth 要截取中文UTF-8字符串的长度
 * @return string
 */
function utf8Substr($str, $start, $lenth)
{
    $len = strlen($str);
    $r = array();
    $n = 0;
    $m = 0;
    for ($i = 0; $i < $len; $i++) {
        $x = substr($str, $i, 1);
        $a = base_convert(ord($x), 10, 2);
        $a = substr('00000000' . $a, -8);
        if ($n < $start) {
            if (substr($a, 0, 1) == 0) {
            } elseif (substr($a, 0, 3) == 110) {
                $i += 1;
            } elseif (substr($a, 0, 4) == 1110) {
                $i += 2;
            }
            $n++;
        } else {
            if (substr($a, 0, 1) == 0) {
                $r[] = substr($str, $i, 1);
            } elseif (substr($a, 0, 3) == 110) {
                $r[] = substr($str, $i, 2);
                $i += 1;
            } elseif (substr($a, 0, 4) == 1110) {
                $r[] = substr($str, $i, 3);
                $i += 2;
            } else {
                $r[] = '';
            }
            if (++$m >= $lenth) {
                break;
            }
        }
    }
    $r = implode('', $r);
    return $r;
}

/**
 * 统计utf8中文字符串长度的函数
 *
 * @param string $str 要计算长度的字符串
 * @return int        返回字符串的长度
 */
function utf8Strlen($str)
{
    if (empty($str)) {
        return 0;
    }
    if (function_exists('mb_strlen')) {
        return mb_strlen($str, 'utf-8');
    } else {
        preg_match_all("/./u", $str, $ar);
        return count($ar[0]);
    }
}

/**
 * 字符截取 支持UTF8/GBK
 * @param $string
 * @param $length
 * @param $dot
 */
function strCut($string, $length, $dot = '...')
{
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
 * xss过滤函数
 *
 * @param $string
 * @return string
 */
function removeXss($string)
{
    $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);
    $parm1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $parm2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $parm = array_merge($parm1, $parm2);
    for ($i = 0; $i < sizeof($parm); $i++) {
        $pattern = '/';
        for ($j = 0; $j < strlen($parm[$i]); $j++) {
            if ($j > 0) {
                $pattern .= '(';
                $pattern .= '(&#[x|X]0([9][a][b]);?)?';
                $pattern .= '|(&#0([9][10][13]);?)?';
                $pattern .= ')?';
            }
            $pattern .= $parm[$i][$j];
        }
        $pattern .= '/i';
        $string = preg_replace($pattern, '', $string);
    }
    return $string;
}

/**
 * 清除html、css、js格式并去除空格
 */
function clearHtml($descclear)
{

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
 * 修复html
 */
function repairHtml($html, $length = null)
{

    $result = '';
    $tagStack = array();
    $len = 0;
    $contents = preg_split("~(<[^>]+?>)~si", $html, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    foreach ($contents as $tag) {
        if (trim($tag) == "") {
            continue;
        }

        if (preg_match("~<([a-z0-9]+)[^/>]*?/>~si", $tag)) {
            $result .= $tag;
        } else if (preg_match("~</([a-z0-9]+)[^/>]*?>~si", $tag, $match)) {
            if ($tagStack[count($tagStack) - 1] == $match[1]) {
                array_pop($tagStack);
                $result .= $tag;
            }
        } else if (preg_match("~<([a-z0-9]+)(?: .*)?(?<![/|/ ])>~si", $tag, $match)) {
            array_push($tagStack, $match[1]);
            $result .= $tag;
        } else if (preg_match("~<!--.*?-->~si", $tag)) {
            $result .= $tag;
        } else {
            if (is_null($length) || $len + mb_strlen($tag) < $length) {
                $result .= $tag;
                $len += mb_strlen($tag);
            } else {
                $str = mb_substr($tag, 0, $length - $len + 1);
                $result .= $str;
                break;
            }
        }
    }
    while (!empty($tagStack)) {
        $result .= '</' . array_pop($tagStack) . '>';
    }
    return $result;
}
