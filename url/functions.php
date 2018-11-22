<?php

/**
 * #######################################
 *
 * url相关代码片段
 *
 * #######################################
 */

/**
 * 获取来源url
 */
function getReferer()
{
    return str_replace(array(SINA_LOGIN_URL, TX_LOGIN_URL), '', $_SERVER['HTTP_REFERER']);
}

/**
 * 获取url后缀
 */
function getUrlSuffix($filename = '')
{
    return strrchr($filename, '.');
}

/**
 * 使用tinyurl生成短网址
 */
function getTinyUrl($url)
{
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, 'http://tinyurl.com/api-create.php?url=' . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

/**
 * 创建数据URI
 */
function dataUri($file, $mime)
{
    $contents = file_get_contents($file);
    $base64 = base64_encode($contents);
    return "data:$mime;base64,$base64";
}

/**
 * 使用 PHP 和 Google 获取域名的 favicon 图标
 */
function getFavicon($url)
{
    $url = str_replace("http://", '', $url);
    return "http://www.google.com/s2/favicons?domain=" . $url;
}
