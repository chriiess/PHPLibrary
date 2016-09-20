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
function getReferer() {
    return str_replace(array(SINA_LOGIN_URL, TX_LOGIN_URL), '', $_SERVER['HTTP_REFERER']);
}

/**
 * 获取url后缀
 */
function getUrlSuffix($filename = '') {
    return strrchr($filename, '.');
}

/**
 * 根据URL下载图片
 */
function imagefromURL($image, $rename) {
    $ch = curl_init($image);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
    $rawdata = curl_exec($ch);
    curl_close($ch);
    $fp = fopen("$rename", 'w');
    fwrite($fp, $rawdata);
    fclose($fp);
}

/**
 * 检测URL是否有效
 */
function isvalidURL($url) {
    $check = 0;
    if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
        $check = 1;
    }
    return $check;
}

/**
 * 使用tinyurl生成短网址
 */
function getTinyUrl($url) {
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
function dataUri($file, $mime) {
    $contents = file_get_contents($file);
    $base64 = base64_encode($contents);
    return "data:$mime;base64,$base64";
}