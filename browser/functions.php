<?php

/**
 * 浏览器相关代码片段
 */

/**
 * 获取浏览器类型
 */
function getBrowser()
{

    $browser = 'other';

    if (strpos($_SERVER["HTTP_USER_AGENT"], "TheWorld") || strpos($_SERVER["HTTP_USER_AGENT"], "QIHU THEWORLD")) {
        $browser = 'world';
    } elseif (strpos($_SERVER["HTTP_USER_AGENT"], "Maxthon")) {
        $browser = 'aoyou';
    } elseif (strpos($_SERVER["HTTP_USER_AGENT"], "TencentTraveler")) {
        //  or (strpos($_SERVER["HTTP_USER_AGENT"], "Trident") AND strpos($_SERVER["HTTP_USER_AGENT"], "SLCC2"))
        $browser = 'telcent';
    } elseif (strpos($_SERVER["HTTP_USER_AGENT"], "SE 2") and strpos($_SERVER["HTTP_USER_AGENT"], "MetaSr")) {
        $browser = 'sogou';
    } elseif (strpos($_SERVER["HTTP_USER_AGENT"], "360SE") and !strpos($_SERVER["HTTP_USER_AGENT"], "TencentTraveler")) {
        $browser = '360';
    } elseif (strpos($_SERVER["HTTP_USER_AGENT"], "QIHU 360EE") and !strpos($_SERVER["HTTP_USER_AGENT"], "TencentTraveler")) {
        $browser = '360';
    } elseif (strpos($_SERVER["HTTP_USER_AGENT"], "MSIE 9.0")) {
        $browser = 'ie9';
    } elseif (strpos($_SERVER["HTTP_USER_AGENT"], "MSIE 8.0")) {
        $browser = 'ie8';
    } elseif (strpos($_SERVER["HTTP_USER_AGENT"], "MSIE 7.0")) {
        $browser = 'ie7';
    } elseif (strpos($_SERVER["HTTP_USER_AGENT"], "MSIE 6.0")) {
        $browser = 'ie6';
    } elseif (strpos($_SERVER["HTTP_USER_AGENT"], "Firefox")) {
        $browser = 'firefox';
    } elseif (strpos($_SERVER["HTTP_USER_AGENT"], "Chrome")) {
        $browser = 'chrome';
    } elseif (strpos($_SERVER["HTTP_USER_AGENT"], "Safari")) {
        $browser = 'safari';
    }

    return $browser;
}

/*
 * 获取目标设备类型
 */
function getEquipment()
{
    $useragent = strtolower($_SERVER["HTTP_USER_AGENT"]);
    // iphone
    $is_iphone = strripos($useragent, 'iphone');
    if ($is_iphone) {
        return 'iphone';
    }
    // android
    $is_android = strripos($useragent, 'android');
    if ($is_android) {
        return 'android';
    }
    // 微信
    $is_weixin = strripos($useragent, 'micromessenger');
    if ($is_weixin) {
        return 'weixin';
    }
    // ipad
    $is_ipad = strripos($useragent, 'ipad');
    if ($is_ipad) {
        return 'ipad';
    }
    // ipod
    $is_ipod = strripos($useragent, 'ipod');
    if ($is_ipod) {
        return 'ipod';
    }
    // pc电脑
    $is_pc = strripos($useragent, 'windows nt');
    if ($is_pc) {
        return 'pc';
    }
    return 'other';
}
